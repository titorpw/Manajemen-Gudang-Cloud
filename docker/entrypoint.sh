#!/bin/sh
set -e

echo "Caching configuration..."
php artisan config:cache

echo "Running migrations..."
php artisan migrate --force

echo "Starting application..."
exec "$@"
