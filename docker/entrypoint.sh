#!/bin/sh
set -e

echo "Caching configuration..."
php artisan config:cache

echo "Running migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

echo "Starting application..."
exec "$@"
