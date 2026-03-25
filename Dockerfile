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

# Configure Apache to serve from Laravel's public/ directory.
# ports.conf is written at runtime in start.sh using the actual $PORT value.
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy the startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

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
RUN composer clear-cache && composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Set environment variables for build
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=https://erpgo-project-524r.onrender.com
ENV ASSET_URL=https://erpgo-project-524r.onrender.com
# Use database sessions so they persist across restarts/redeploys on Render
ENV SESSION_DRIVER=database
# Render terminates HTTPS at its proxy — cookies must be marked secure
ENV SESSION_SECURE_COOKIE=true

# Install Node dependencies and build frontend assets
RUN npm cache clean --force && npm install --legacy-peer-deps --no-audit && npm install glob --save-dev && npm run build

# Set ownership for the entire application to www-data
RUN chown -R www-data:www-data /var/www/html

# Run migrations during build (best-effort; also runs at container start)
RUN php artisan config:clear && php artisan migrate --force || echo "Migration completed or failed"

# Render injects $PORT at runtime; start.sh configures Apache to listen on it.
# EXPOSE is a hint only — the actual port is dynamic.
EXPOSE 10000

# Start via script: runs migrations, seeds, then starts Apache
CMD ["/start.sh"]
