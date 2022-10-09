FROM composer:2 as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install --no-dev -o -a --no-scripts --ignore-platform-reqs; rm -rf composer*

FROM ghcr.io/medleybox/php-fpm:master as webapp

ENV POSTGRES_DB=medleybox_webapp

COPY bin/console /var/www/bin/console
COPY public/index.php /var/www/public/index.php
COPY docker/app/php.ini /usr/local/etc/php/conf.d/webapp.ini
COPY templates /var/www/templates
COPY --from=composer /app /var/www
COPY --from=ghcr.io/medleybox/encore:master /app/public/build /var/www/public/build

USER root

RUN chown -Rf 82:82 /var/www /tmp

USER 82

RUN chmod +x bin/console
