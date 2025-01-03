FROM php:8.2-fpm

LABEL maintainer="Moein Bakhtnama"

# Install system dependencies
RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    unzip \
    zip \
    libzip-dev \
    supervisor \
    netcat-openbsd \
    libicu-dev \
    && docker-php-ext-install \
    intl \
    zip \
    pcntl \
    gettext

# Install Redis and MySQL extensions using PHPIZE_DEPS
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pdo_mysql

# Install Node.js LTS
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# Install Composer LTS
COPY --from=composer:lts /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

COPY ./storage/app/default.png ./storage/app/public/images/avatars/default.png

# Set permissions for Laravel directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run composer install
RUN composer install --no-dev --optimize-autoloader

# Run npm install and build
RUN npm install && npm run build

# Copy supervisor configuration files
COPY docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/app/start-container /usr/local/bin/start-container
COPY docker/app/php.ini /usr/local/etc/php/conf.d/99-linkedmeet.ini
RUN chmod +x /usr/local/bin/start-container

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Expose PHP-FPM port
EXPOSE 9000

# Expose Laravel Reverb WebSocket port
EXPOSE 8080

ENTRYPOINT ["start-container"]
