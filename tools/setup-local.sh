#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP_DIR="$ROOT/wp"
DB_NAME="${DB_NAME:-okoyom}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"
PORT="${PORT:-8080}"

say() { printf '\033[1m%s\033[0m\n' "$*"; }

command -v php   >/dev/null || { echo "Нужен PHP"; exit 1; }
command -v mysql >/dev/null || { echo "Нужен клиент mysql"; exit 1; }

mysqladmin ping >/dev/null 2>&1 || {
  echo "MySQL не отвечает. Запустите сервер и повторите."
  exit 1
}

if [ ! -f "$WP_DIR/wp-load.php" ]; then
  say "Скачиваю ядро WordPress с wordpress.org"
  mkdir -p "$WP_DIR"
  curl -fsSL https://ru.wordpress.org/latest-ru_RU.tar.gz -o /tmp/wordpress.tar.gz
  tar -xzf /tmp/wordpress.tar.gz -C "$WP_DIR" --strip-components=1
  rm -f /tmp/wordpress.tar.gz
else
  say "Ядро WordPress уже на месте"
fi

say "Готовлю базу $DB_NAME"
if [ -n "$DB_PASSWORD" ]; then
  MYSQL_ARGS=(-u "$DB_USER" -p"$DB_PASSWORD")
else
  MYSQL_ARGS=(-u "$DB_USER")
fi
mysql "${MYSQL_ARGS[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ ! -f "$WP_DIR/wp-config.php" ]; then
  say "Пишу wp-config.php"
  SALTS="$(curl -fsSL https://api.wordpress.org/secret-key/1.1/salt/)"
  cat > "$WP_DIR/wp-config.php" <<PHP
<?php
define( 'DB_NAME', '$DB_NAME' );
define( 'DB_USER', '$DB_USER' );
define( 'DB_PASSWORD', '$DB_PASSWORD' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$SALTS

\$table_prefix = 'oko_';

// Окружение пишется в каждую заявку и отличает тестовые письма от боевых.
define( 'OKOYOM_ENV', 'local' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
// PHP 8.5 новее, чем официально поддерживает WordPress: шум deprecation
// не должен лезть в разметку и портить проверку вёрстки.
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', true );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
PHP
else
  say "wp-config.php уже есть"
fi

say "Подключаю тему и плагин симлинками"
mkdir -p "$ROOT/uploads"
ln -sfn "$ROOT/wp-content/themes/okoyom"        "$WP_DIR/wp-content/themes/okoyom"
ln -sfn "$ROOT/wp-content/plugins/okoyom-core"  "$WP_DIR/wp-content/plugins/okoyom-core"
ln -sfn "$ROOT/uploads"                          "$WP_DIR/wp-content/uploads"

say "Готово. Дальше:"
cat <<TXT

  php -S 127.0.0.1:$PORT -t $WP_DIR

  Открыть http://127.0.0.1:$PORT — пройти установку WordPress,
  затем в админке:
    Внешний вид → Темы       → включить Okoyom
    Плагины                  → включить WooCommerce и «Окоём — ядро»

  WooCommerce ставится из каталога плагинов: он обязателен по ТЗ,
  без него таксономии фильтров и товары не регистрируются.
TXT
