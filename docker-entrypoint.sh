#!/bin/sh
set -e

# Clear any stale compiled views or caches on container start
php artisan view:clear || true
php artisan config:clear || true
php artisan cache:clear || true

# Ensure storage permissions (best-effort)
chown -R www-data:www-data storage bootstrap/cache || true

# Execute the passed command
exec "$@"
