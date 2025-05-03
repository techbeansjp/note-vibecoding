# Note VibeCoding - Backend

This repository contains the backend API for the VibeCoding project, built with Laravel, MySQL, Mailhog, and MinIO.

## Requirements

- Docker
- Docker Compose

## Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/techbeansjp/note-vibecoding.git
   cd note-vibecoding
   ```

2. Start the Docker environment:
   ```bash
   docker-compose up -d
   ```

3. The Laravel application will be automatically installed on the first run.

4. Access the services:
   - API: http://localhost:8000
   - Mailhog: http://localhost:8025
   - MinIO Console: http://localhost:9001 (Login with minio/password)

## Services

- **Laravel API**: The main application built with Laravel latest version
- **MySQL**: Database server
- **Mailhog**: Email testing service
- **MinIO**: S3-compatible object storage

## Development

To run artisan commands:
```bash
docker-compose exec app php artisan <command>
```

To run migrations:
```bash
docker-compose exec app php artisan migrate
```

To access the database:
```bash
docker-compose exec db mysql -u vibecoding -p vibecoding
```
