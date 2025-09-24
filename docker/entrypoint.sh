#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"
echo "[entrypoint] Starting on port ${PORT}"

# (optional) silence Apache warning if you still use Apache later
printf "ServerName localhost\n" > /etc/apache2/conf-available/servername.conf || true
a2enconf servername >/dev/null 2>&1 || true

# ----- Laravel prep -----
if [ -z "${APP_KEY:-}" ] || ! grep -q "base64:" <<<"$APP_KEY"; then
  php artisan key:generate --force || true
fi

php artisan storage:link || true
php artisan package:discover --ansi || true

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php artisan migrate --force || true

# ----- Start HTTP server bound to $PORT -----
# Use PHP built-in server (keeps container in foreground)
echo "[entrypoint] php -S 0.0.0.0:${PORT} -t public public/index.php"
exec php -S 0.0.0.0:${PORT} -t public public/index.php
