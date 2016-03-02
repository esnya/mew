FROM php:7.0-apache
MAINTAINER ukatama dev.ukatama@gmail.com

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update -y -q
RUN apt-get install -y -q git

WORKDIR /var/www/html
COPY composer.json /var/www/html
RUN composer install

COPY . /var/www/html
COPY config/wiki.default.php /var/www/html/config/wiki.php

