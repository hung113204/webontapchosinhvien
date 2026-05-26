FROM php:8.2-apache

# Cài đặt các thư viện cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Bật Apache Rewrite Module để chạy được Route Laravel
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Cấu hình chỉ thẳng vào thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy toàn bộ code vào server
WORKDIR /var/www/html
COPY . .

# Cài đặt các package PHP (Composer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền ghi file cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["apache2-foreground"]