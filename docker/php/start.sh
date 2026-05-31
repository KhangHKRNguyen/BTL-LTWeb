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

php-fpm
