#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

rm -f public/storage
ln -s /var/www/html/storage/app/public public/storage

database_file="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
fresh_database=0
if [ ! -s "$database_file" ]; then
    mkdir -p "$(dirname "$database_file")"
    touch "$database_file"
    fresh_database=1
fi

chmod 664 "$database_file"
chown www-data:www-data "$database_file"
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force

if [ "$fresh_database" -eq 1 ]; then
    php artisan db:seed --force
fi

# The database-backed cache table does not exist until migrations have run on a
# first deployment. Clearing all caches before migrating makes a fresh
# container exit with "no such table: cache".
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
