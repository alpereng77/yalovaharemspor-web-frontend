FROM php:8.2-fpm-alpine

# Gerekli sistem paketleri ve PHP eklentileri
RUN apk add --no-cache nginx git curl libpng-dev libxml2-dev zip unzip oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd bcmath

# Composer kurulumu
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . .

# Composer bağımlılıklarını kur
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Dosya izinleri
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Port ve başlangıç komutu
EXPOSE 80
CMD php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=80
