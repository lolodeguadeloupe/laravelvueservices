#!/bin/bash

# ServicesPro - Deploy Script for Coolify
# This script handles the deployment process

set -e

echo "🚀 Starting ServicesPro deployment..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
until timeout 1 bash -c 'cat < /dev/null > /dev/tcp/mysql/3306'; do
    echo "Database not ready yet..."
    sleep 2
done
echo "✅ Database is ready!"

# Wait for Redis to be ready
echo "⏳ Waiting for Redis..."
until timeout 1 bash -c 'cat < /dev/null > /dev/tcp/redis/6379'; do
    echo "Redis not ready yet..."
    sleep 2
done
echo "✅ Redis is ready!"

# Install dependencies if not already installed
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm ci --omit=dev
fi

# Build frontend assets
echo "🏗️ Building frontend assets..."
npm run build

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "⚙️ Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Seed initial data if needed (only on first deploy)
if [ "$INITIAL_DEPLOY" = "true" ]; then
    echo "🌱 Seeding initial data..."
    php artisan db:seed --force
fi

# Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Set correct permissions
echo "🔐 Setting permissions..."
chown -R www:www /var/www/html/storage
chown -R www:www /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ ServicesPro deployment completed successfully!"

# Start the web server
exec "$@"