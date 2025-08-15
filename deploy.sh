# Use official PHP 8.3 Apache image
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
RUN composer install --no-dev --no-scripts --verbose

# Copy project files
COPY . .

# Configure Apache for Laravel
RUN a2enmod rewrite \
    && echo 'DocumentRoot /var/www/html/public' > /etc/apache2/sites-available/000-default.conf \
    && echo '<VirtualHost *:80>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create required directories
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache \
    && mkdir -p bootstrap/cache

# Laravel Setup Commands (moved from deploy.sh)
# These commands will run during Docker build with environment variables from Render
RUN echo "#!/bin/bash" > /usr/local/bin/laravel-setup.sh \
    && echo "set -e" >> /usr/local/bin/laravel-setup.sh \
    && echo "echo 'Starting Laravel deployment...'" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Clear any existing caches" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan config:clear || true" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan cache:clear || true" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan view:clear || true" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan route:clear || true" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Generate application key if needed" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan key:generate --force" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Run database migrations" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan migrate --force" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Cache configurations for production" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan config:cache" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan route:cache" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan view:cache" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Create storage link for public files" >> /usr/local/bin/laravel-setup.sh \
    && echo "php artisan storage:link || true" >> /usr/local/bin/laravel-setup.sh \
    && echo "echo 'Laravel deployment complete!'" >> /usr/local/bin/laravel-setup.sh \
    && echo "# Start Apache" >> /usr/local/bin/laravel-setup.sh \
    && echo "exec apache2-foreground" >> /usr/local/bin/laravel-setup.sh \
    && chmod +x /usr/local/bin/laravel-setup.sh

EXPOSE 80

# Use the setup script as the entry point
CMD ["/usr/local/bin/laravel-setup.sh"]
