

rm -rf /var/www/html/*

composer create-project --prefer-dist laravel/laravel .

chown -R www:www /var/www/html

php artisan key:generate

php artisan storage:link

php artisan migrate

echo "Laravel has been initialized successfully!"
