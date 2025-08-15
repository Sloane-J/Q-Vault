#!/bin/bash

# Render Build Script
# Place this in your repository root as deploy.sh

set -e  # Exit on error

echo "Starting Laravel deployment..."

# Create storage directories if they don't exist
mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache
mkdir -p bootstrap/cache

# Set proper permissions
chmod -R 755 storage bootstrap/cache

# Clear any existing caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Install/update composer dependencies (if not done in Docker)
# composer install --no-dev --optimize-autoloader

# Generate application key if needed
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link for public files
php artisan storage:link

echo "Deployment complete!"
