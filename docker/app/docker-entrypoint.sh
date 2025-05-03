set -e

if [ ! -f /var/www/html/artisan ]; then
    echo "Creating new Laravel project..."
    composer create-project --prefer-dist laravel/laravel:^10.0 /var/www/html
    
    chown -R www:www /var/www/html
    
    php artisan key:generate
    
    php artisan storage:link
    
    php artisan migrate
    
    echo "Laravel has been initialized successfully!"
else
    echo "Laravel is already installed."
fi

exec "$@"
