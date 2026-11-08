FROM php:8.2-apache

# PDO MySQL + GD (xử lý ảnh) + bật mod_rewrite cho .htaccess
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Cho phép .htaccess hoạt động (AllowOverride All)
RUN sed -ri 's#AllowOverride None#AllowOverride All#g' /etc/apache2/apache2.conf

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html
