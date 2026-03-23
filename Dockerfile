# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    curl \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pgsql pdo_pgsql zip gd exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Copy .env.example to .env if .env doesn't exist
RUN if [ ! -f /var/www/html/.env ]; then \
    cp /var/www/html/.env.example /var/www/html/.env; \
    fi

# Generate APP_KEY if not set
RUN php artisan key:generate --force || true

# Install PHP dependencies
# Clean composer cache first
RUN composer clear-cache && composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Set environment variables for build
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=https://biz-click.onrender.com
ENV ASSET_URL=https://biz-click.onrender.com

# Install Node dependencies and build
# Install glob explicitly as it's required by vite.config.js
RUN npm cache clean --force && npm install --legacy-peer-deps --no-audit && npm install glob --save-dev && npm run build

# Set ownership for the entire application to www-data
RUN chown -R www-data:www-data /var/www/html

# Run migrations during build
RUN php artisan config:clear && php artisan migrate --force || echo "Migration completed or failed"

# Create a startup script that runs migrations and seeds
RUN echo '#!/bin/bash\nphp artisan config:clear\nphp artisan migrate --force\nphp artisan db:seed --force\ntouch /var/www/html/storage/installed\nln -sf /var/www/html/storage/app/public /var/www/html/public/storage\napache2-foreground' > /start.sh && chmod +x /start.sh

# Configure Apache to listen on port 10000
RUN echo "Listen 10000" >> /etc/apache2/ports.conf

# Expose port 10000
EXPOSE 10000

# Start the startup script
CMD ["/start.sh"]
