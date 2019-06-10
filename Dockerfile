FROM php:7.2-fpm
WORKDIR /app

RUN docker-php-ext-install mysqli && \
    apt-get update && \
    apt-get install -y mysql-client
