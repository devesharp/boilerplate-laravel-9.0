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
RUN composer install
#RUN composer dump-autoload -o

# ------------------------
# Create App
# ------------------------
FROM devesharp/nginx:php-8.1-alpine as app
COPY --from=build /app .
RUN chown -R www-data:www-data /app

## Prepare required directories for Newrelic installation
#RUN mkdir -p /var/log/newrelic /var/run/newrelic && \
#    touch /var/log/newrelic/php_agent.log /var/log/newrelic/newrelic-daemon.log && \
#    chmod -R g+ws /tmp /var/log/newrelic/ /var/run/newrelic/ && \
#    chown -R 1001:0 /tmp /var/log/newrelic/ /var/run/newrelic/ && \
#    # Download and install Newrelic binary
#    export NEWRELIC_VERSION=$(curl -sS https://download.newrelic.com/php_agent/release/ | sed -n 's/.*>\(.*linux-musl\).tar.gz<.*/\1/p') && \
#    cd /tmp && curl -sS "https://download.newrelic.com/php_agent/release/${NEWRELIC_VERSION}.tar.gz" | gzip -dc | tar xf - && \
#    cd "${NEWRELIC_VERSION}" && \
#    NR_INSTALL_SILENT=true ./newrelic-install install && \
#    rm -f /var/run/newrelic-daemon.pid && \
#    rm -f /tmp/.newrelic.sock && \
#    # For Newrelic's APM (Application Monitoring) license and appname are required.
#    # Enviroment variables `NEW_RELIC_LICENSE_KEY` and `NEW_RELIC_APP_NAME` are required when buidling Docker image,
#    # so you must set them in your **BuildConfig** Environments.
#    sed -i \
#        -e "s/newrelic.license =.*/newrelic.license = ${NEW_RELIC_LICENSE_KEY}/" \
#        -e "s/newrelic.appname =.*/newrelic.appname = ${NEW_RELIC_APPNAME}/" \
#        /usr/local/etc/php/conf.d/newrelic.ini

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
