FROM php:7.4-fpm
WORKDIR /app

RUN docker-php-ext-install mysqli && \
    apt-get update && \
    apt-get install -y mariadb-client
