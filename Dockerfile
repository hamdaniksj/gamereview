# Gunakan image resmi PHP dengan ekstensi umum Laravel
FROM php:8.2-fpm

# Install dependencies sistem & ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan direktori kerja di dalam container
WORKDIR /var/www/html

# 1. SALIN SELURUH FILE PROJECT KE DALAM CONTAINER (PENTING AGAR COMPOSER TERBACA)
COPY . .

# 2. Salin konfigurasi Nginx kustom
COPY docker/default.conf /etc/nginx/sites-available/default

# 3. Baru jalankan Composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan hak akses (permissions) untuk storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 untuk web server
EXPOSE 80

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD service nginx start && php-fpm