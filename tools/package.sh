#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST="$ROOT/dist"
DB_NAME="${DB_NAME:-okoyom}"
DB_USER="${DB_USER:-root}"

mkdir -p "$DIST"

echo "— дамп базы $DB_NAME"
mysqldump -u "$DB_USER" --single-transaction --default-character-set=utf8mb4 "$DB_NAME" \
  | gzip > "$DIST/okoyom-db.sql.gz"

echo "— архив файлов (симлинки разворачиваются)"
tar -czf "$DIST/okoyom-site.tar.gz" \
  -C "$ROOT" \
  -h \
  --exclude='wp/wp-content/uploads/cache' \
  --exclude='.DS_Store' \
  wp

echo "— готово:"
ls -lh "$DIST" | awk 'NR>1 {print "   " $NF, $5}'

cat <<'TXT'

Развёртывание на сервере:
  1. Залить оба файла (scp/sftp) и распаковать сайт:
       tar -xzf okoyom-site.tar.gz          # появится каталог wp/
     Содержимое wp/ положить в корень сайта (public_html / www).
  2. Создать БД и пользователя, импортировать дамп:
       gunzip < okoyom-db.sql.gz | mysql -u USER -p DBNAME
  3. В wp-config.php поправить DB_NAME / DB_USER / DB_PASSWORD / DB_HOST.
  4. Адрес сайта настраивать не нужно: WP_HOME берётся из запроса.
  5. Пока сайт не сдан: Настройки → Чтение → «Попросить поисковые системы
     не индексировать» (ТЗ п. 2.4), либо basic auth на поддомене.
TXT
