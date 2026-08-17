#!/bin/bash
# Run on VPS after git pull
set -e
cd /var/www/hospital

mkdir -p public/Dashboard/img/doctors
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache public/Dashboard/img
chmod -R 775 storage bootstrap/cache public/Dashboard/img

sudo -u www-data php artisan config:cache
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear
sudo -u www-data composer dump-autoload --no-interaction

echo "Done. Permissions and cache updated."
