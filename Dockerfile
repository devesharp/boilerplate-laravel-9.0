FROM composer:2.0.8 as composer

# ------------------------
# Install vendor
# ------------------------
FROM devesharp/nginx:php-8.1-alpine as build
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

# Build prod
COPY . /app
COPY ./public /app/html
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --no-scripts
RUN composer dump-autoload -o

# ------------------------
# Create App
# ------------------------
FROM devesharp/nginx:php-8.1-alpine as app
RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql
COPY --from=build /app .
RUN chown -R www-data:www-data /app

# ------------------------
# Creaate Test
# ------------------------
FROM app as test
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install
RUN composer dump-autoload -o

RUN apk add --no-cache openssl
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz
