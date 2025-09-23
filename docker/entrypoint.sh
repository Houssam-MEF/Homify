#!/usr/bin/env bash
set -e

# Generate key if missing
if [ -z "${APP_KEY:-}" ] || ! grep -q "base64:" <<<"$APP_KEY"; then
  php artisan key:generate --force
fi

# Storage symlink (ignore if exists)
php artisan storage:link || true

# Cache configs/routes/views for perf
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (no-op if already applied)
php artisan migrate --force || true

# Health endpoint (optional—kept simple by routes/web.php too)
# php artisan schedule:run &  # uncomment if you want to background the scheduler

# Hand off to Apache
apache2-foreground
