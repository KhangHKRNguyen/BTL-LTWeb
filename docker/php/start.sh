#!/bin/sh
set -e

if ! php -m | grep -qi '^pdo_mysql$'; then
    apt-get update
    apt-get install -y \
        default-mysql-client \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        zip
    docker-php-ext-install bcmath exif gd mbstring pcntl pdo_mysql zip
fi

# Configure file upload limits dynamically
echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini
echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

php-fpm
