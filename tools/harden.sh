#!/usr/bin/env bash
set -e
CFG=/etc/nginx/sites-available/okoem
grep -q xmlrpc "$CFG" || sed -i 's|listen 80 default_server;|listen 80 default_server;\n    location = /xmlrpc.php { deny all; return 403; }|' "$CFG"
nginx -t && systemctl reload nginx && echo "xmlrpc закрыт, nginx перезагружен"
