#!/bin/sh
set -e

php-fpm -D
sleep 2

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

nginx -g 'daemon off;'