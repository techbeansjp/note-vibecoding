

echo "Setting up Note VibeCoding backend..."

docker-compose up -d

echo "Waiting for containers to be ready..."
sleep 10

echo "Checking Laravel installation..."
docker-compose exec -T app php artisan --version

echo "Running migrations to verify database connection..."
docker-compose exec -T app php artisan migrate

echo "Setup completed successfully!"
echo "You can access the services at:"
echo "- API: http://localhost:8000"
echo "- Mailhog: http://localhost:8025"
echo "- MinIO Console: http://localhost:9001 (Login with minio/password)"
