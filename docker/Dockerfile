FROM php:8.0.14-fpm
RUN apt-get update \
    && apt-get install -y nano vim wget git unzip zlib1g-dev libpng-dev libzip-dev libpq-dev \
    && pecl install xdebug-3.1.2 \
    && docker-php-ext-enable xdebug \
    && printf "\n" | pecl install apcu-5.1.21 \
    && docker-php-ext-enable apcu

RUN docker-php-ext-install pdo bcmath zip gd sockets pdo_pgsql pgsql

ADD ./php/php.ini /usr/local/etc/php/php.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN curl --remote-name --time-cond ./php/cacert.pem https://curl.se/ca/cacert.pem

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www