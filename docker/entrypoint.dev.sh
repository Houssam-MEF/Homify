#!/bin/bash
set -e

echo "Starting development environment..."

# Wait for database
echo "Waiting for database..."
while ! php artisan migrate:status > /dev/null 2>&1; do
    echo "Database not ready, waiting..."
    sleep 2
done

# Generate key if needed
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start Vite dev server in background
echo "Starting Vite dev server..."
npm run dev &
VITE_PID=$!

# Start Apache in foreground
echo "Starting Apache server..."
exec apache2-foreground
