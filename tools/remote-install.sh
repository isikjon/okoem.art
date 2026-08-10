#!/usr/bin/env bash

set -euo pipefail

DOMAIN="${1:-okoem.art}"
SITE_DIR="/var/www/okoem"
DB_NAME="okoyom"
DB_USER="okoyom"
PASS_FILE="/root/.okoyom-db-pass"

export DEBIAN_FRONTEND=noninteractive

echo "== пакеты =="
apt-get update -q
apt-get install -yq nginx mariadb-server \
  php-fpm php-mysql php-xml php-mbstring php-curl php-gd php-zip php-intl \
  unzip curl > /dev/null

PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
PHP_SOCK="/run/php/php${PHP_VER}-fpm.sock"
echo "PHP $PHP_VER, сокет $PHP_SOCK"

echo "== база =="
if [ ! -f "$PASS_FILE" ]; then
  head -c 64 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | cut -c1-24 > "$PASS_FILE"
  chmod 600 "$PASS_FILE"
fi
DB_PASS="$(cat "$PASS_FILE")"

mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

TABLES="$(mysql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME'")"
if [ "$TABLES" = "0" ]; then
  echo "импорт дампа"
  gunzip < /root/okoyom-db.sql.gz | mysql "$DB_NAME"
else
  echo "БД не пуста ($TABLES таблиц) — импорт пропущен"
fi

echo "== файлы =="
mkdir -p "$SITE_DIR"
tar -xzf /root/okoyom-site.tar.gz -C "$SITE_DIR"

echo "== wp-config: доступы сервера =="
CFG="$SITE_DIR/wp/wp-config.php"
php <<PHP
<?php
\$cfg = file_get_contents('$CFG');
\$cfg = preg_replace("/define\( 'DB_NAME', '[^']*' \)/", "define( 'DB_NAME', '$DB_NAME' )", \$cfg);
\$cfg = preg_replace("/define\( 'DB_USER', '[^']*' \)/", "define( 'DB_USER', '$DB_USER' )", \$cfg);
\$cfg = preg_replace("/define\( 'DB_PASSWORD', '[^']*' \)/", "define( 'DB_PASSWORD', '$DB_PASS' )", \$cfg);
\$cfg = preg_replace("/define\( 'DB_HOST', '[^']*' \)/", "define( 'DB_HOST', 'localhost' )", \$cfg);
\$cfg = str_replace("define( 'OKOYOM_ENV', 'local' )", "define( 'OKOYOM_ENV', 'prod' )", \$cfg);
\$cfg = str_replace("define( 'WP_DEBUG', true )", "define( 'WP_DEBUG', false )", \$cfg);
file_put_contents('$CFG', \$cfg);
echo "wp-config обновлён\n";
PHP

chown -R www-data:www-data "$SITE_DIR"
find "$SITE_DIR" -type d -exec chmod 755 {} +
find "$SITE_DIR" -type f -exec chmod 644 {} +

echo "== nginx =="
cat > /etc/nginx/sites-available/okoem <<NGINX
server {
    listen 80 default_server;
    server_name $DOMAIN www.$DOMAIN $(hostname -I | awk "{print \$1}");

    root $SITE_DIR/wp;
    index index.php;

    client_max_body_size 64m;

    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:$PHP_SOCK;
    }

    location ~* \.(css|js|png|jpe?g|webp|svg|woff2?)\$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
NGINX

rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/okoem /etc/nginx/sites-enabled/okoem
nginx -t
systemctl reload nginx
systemctl enable --now "php${PHP_VER}-fpm" nginx mariadb > /dev/null 2>&1 || true

echo "== настройки WP под сервер =="
sudo -u www-data php -r '
$_SERVER["HTTP_HOST"] = "'"$DOMAIN"'";
$_SERVER["REQUEST_URI"] = "/"; $_SERVER["REQUEST_METHOD"] = "GET";
$_SERVER["SERVER_NAME"] = "'"$DOMAIN"'"; $_SERVER["SERVER_PORT"] = "80";
define("WP_USE_THEMES", false);
require "'"$SITE_DIR"'/wp/wp-load.php";
update_option("woocommerce_coming_soon", "no");
update_option("blog_public", 0);
flush_rewrite_rules();
echo "coming-soon выключен, индексация закрыта до запуска, пермалинки обновлены\n";
'

echo "== системный cron для ретраев заявок (WP-Cron ненадёжен) =="
cat > /etc/cron.d/okoyom <<CRON
*/15 * * * * www-data php -r '\$_SERVER["HTTP_HOST"]="$DOMAIN";\$_SERVER["REQUEST_URI"]="/";\$_SERVER["REQUEST_METHOD"]="GET";\$_SERVER["SERVER_NAME"]="$DOMAIN";\$_SERVER["SERVER_PORT"]="80";define("WP_USE_THEMES",false);require "$SITE_DIR/wp/wp-load.php";do_action("okoyom_lead_retry");' > /dev/null 2>&1
CRON

echo
echo "== ГОТОВО =="
echo "Сайт: http://$DOMAIN (и http://$(hostname -I | awk "{print \$1}"))"
echo "Пароль БД: $PASS_FILE (только root)"
curl -s -o /dev/null -w "локальная проверка: HTTP %{http_code}\n" "http://127.0.0.1/" -H "Host: $DOMAIN"
