#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"
echo "[entrypoint] Will listen on port ${PORT}"

# Make Apache listen on $PORT
sed -ri "s/^Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!" /etc/apache2/sites-available/000-default.conf
printf "ServerName localhost\n" > /etc/apache2/conf-available/servername.conf || true
a2enconf servername >/dev/null 2>&1 || true

# Laravel prep
if [ -z "${APP_KEY:-}" ] || ! grep -q "base64:" <<<"$APP_KEY"; then php artisan key:generate --force || true; fi
php artisan storage:link || true
php artisan package:discover --ansi || true

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# (Optional) migrations; you can also move this to Pre-Deploy in Render
php artisan migrate --force || true

echo "[entrypoint] starting apache2-foreground on :${PORT}"
exec apache2-foreground
