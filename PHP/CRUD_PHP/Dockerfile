FROM php:5.6-apache

RUN apt-get update

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli

RUN apt-get clean
RUN apt-get autoclean