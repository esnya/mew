FROM php:apache
MAINTAINER ukatama dev.ukatama@gmail.com

RUN apt-get update -yq

# Install git
RUN apt-get install -yq git

## Install PHP zip extension
RUN apt-get install -yq zlib1g-dev && docker-php-ext-install -j$(nproc) zip

## Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

## Install dependics
WORKDIR /var/www/html
COPY composer.json /var/www/html
RUN composer install

## Add soruce codes
COPY . /var/www/html
COPY config/wiki.default.php /var/www/html/config/wiki.php

## Run unittests
RUN ./vendor/bin/phpunit
