#!/usr/bin/env bash
set -e

# --- make Apache listen on $PORT (Render sets this env var) ---
if [ -n "${PORT:-}" ]; then
  sed -ri "s/^Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
  sed -ri "s!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!" /etc/apache2/sites-available/000-default.conf
fi

# optional: silence ServerName warning
printf "ServerName localhost\n" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# --- Laravel prep ---
# Generate key if missing
if [ -z "${APP_KEY:-}" ] || ! grep -q "base64:" <<<"$APP_KEY"; then
  php artisan key:generate --force
fi

php artisan storage:link || true
php artisan package:discover --ansi || true

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force || true

# health route handled in routes/web.php

# --- start Apache in foreground ---
apache2-foreground
