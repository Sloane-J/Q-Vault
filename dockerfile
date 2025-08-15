# Use official PHP 8.2 FPM image
FROM php:8.3-apache

# Set Composer's memory limit
ENV COMPOSER_MEMORY_LIMIT=-1

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring bcmath exif pcntl gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
# Note: --no-optimize-autoloader is temporarily removed to test if that was causing the issue.
# You can add it back later if the build is stable.
RUN composer install --no-dev --no-scripts --verbose

# Copy project files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create required directories
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache \
    && mkdir -p bootstrap/cache

# Don't run Laravel commands during build - they need environment variables
# These will be run in Render's build/deploy phase

EXPOSE 80
