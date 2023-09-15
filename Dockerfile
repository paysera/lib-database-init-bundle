ARG PHP_VERSION
FROM php:${PHP_VERSION}-cli-alpine
WORKDIR /app

RUN docker-php-ext-install mysqli && \
    apk update && \
    apk add mariadb-client && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/bin/sh"]
