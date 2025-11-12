#!/bin/bash
set -e

# Function to handle errors gracefully
handle_error() {
    echo "Error occurred but continuing..."
    return 0
}

# Copy .env.example to .env if .env doesn't exist (create early for docker-compose)
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "Creating .env file from .env.example..."
        cp .env.example .env
    fi
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "MySQL is ready!"

# Wait for Redis to be ready
echo "Waiting for Redis to be ready..."
while ! nc -z redis 6379; do
  sleep 1
done
echo "Redis is ready!"

# Fix permissions for www-data (needs to be done as root)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Install Composer dependencies (always check, volume might be empty)
if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    # Fix vendor permissions
    chown -R www-data:www-data /var/www/html/vendor 2>/dev/null || true
else
    echo "Composer dependencies already installed"
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders (ignore errors if data already exists)
echo "Running database seeders..."
set +e  # Temporarily disable exit on error
php artisan db:seed --force
SEEDER_EXIT=$?
set -e  # Re-enable exit on error
if [ $SEEDER_EXIT -ne 0 ]; then
    echo "Seeder encountered an error (may be due to existing data), continuing..."
fi

# Clear and cache config
php artisan config:clear
php artisan cache:clear

echo "Setup complete! Starting PHP-FPM..."

# Start PHP-FPM as root (it will drop privileges to www-data)
exec php-fpm

