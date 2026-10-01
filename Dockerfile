FROM php:8.1-fpm

# Install sistem dependensi termasuk libpq-dev untuk PostgreSQL
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP (Gunakan pdo_pgsql)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Copy konfigurasi Nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Copy aplikasi
WORKDIR /var/www
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Jalankan Migrasi & Service
CMD php artisan migrate --force && service nginx start && php-fpm
