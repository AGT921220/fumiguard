#!/usr/bin/env sh
set -e

cd /var/www/html

# Ensure env file exists for Laravel bootstrap.
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
  cp .env.example .env
fi

# Install dependencies (kept in a named volume to avoid host overrides).
if [ ! -f "vendor/autoload.php" ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if it's missing (do not run migrations here).
if [ -f ".env" ] && grep -q '^APP_KEY=$' .env; then
  php artisan key:generate --force
fi

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"

