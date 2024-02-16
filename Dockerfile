FROM php:8.2-cli

RUN apt-get update && apt-get install -y zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

RUN mkdir -p devoted/memory-db

COPY . /devoted/memory-db

RUN sh /devoted/memory-db/bin/install-composer.sh

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --working-dir=/devoted/memory-db

WORKDIR /devoted/memory-db
