FROM composer:2.0.8 as composer
FROM devesharp/php:php8.1-nginx as build
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

RUN apt-get install -y zip unzip

# Build prod
COPY . /app
COPY ./public /app/html
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --no-scripts
RUN composer dump-autoload -o


FROM devesharp/php:php8.1-nginx as app
WORKDIR /app
COPY --from=build /app .
RUN chown -R www-data:www-data /app


FROM app as test
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apt-get install -y zip unzip

RUN composer install
RUN composer dump-autoload -o

RUN apt-get update && apt-get install -y wget openssl libssl-dev

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz
