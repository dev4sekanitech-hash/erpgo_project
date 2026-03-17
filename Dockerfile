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
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies
# First try with composer.lock, then fallback to update if needed
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction || composer update --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Install Node dependencies and build
# Install glob explicitly as it's required by vite.config.js
RUN npm cache clean --force && npm install --legacy-peer-deps --no-audit && npm install glob --save-dev && npm run build

# Set permissions
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations during build
RUN php artisan config:clear && php artisan migrate --force || echo "Migration completed or failed"

# Expose port 10000
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
