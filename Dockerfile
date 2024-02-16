FROM php:8.2-cli

# This is needed to get composer to be able to unpack libraries it pulls
RUN apt-get update && apt-get install -y zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Make an app directory
RUN mkdir -p devoted/memory-db

# Copy all code to app directory
COPY . /devoted/memory-db

# Install composer, the package manager for PHP
RUN sh /devoted/memory-db/bin/install-composer.sh

# Install php library dependencies
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --working-dir=/devoted/memory-db

# Tell this image to always begin working in this directory
WORKDIR /devoted/memory-db
