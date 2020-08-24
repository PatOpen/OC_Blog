# Base image
FROM php:7.3-fpm as base

WORKDIR /usr/src/app

RUN pecl install apcu && \
    apt-get update && \
    apt-get install -y libzip-dev curl libicu-dev g++ && \
    docker-php-ext-install zip && \
    docker-php-ext-enable apcu && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install intl && \
    apt-get install -y nginx supervisor

COPY ./default.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 

COPY ./composer.json ./

ENTRYPOINT ["/usr/bin/supervisord"]


