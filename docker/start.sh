#!/bin/sh
set -e

# Start php-fpm first
php-fpm -D

# Wait for php-fpm to be ready
sleep 2

# Run Laravel commands
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start nginx in foreground (keeps container alive)
nginx -g 'daemon off;'