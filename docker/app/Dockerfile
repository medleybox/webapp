FROM composer:2 as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install --no-dev -o -a --no-scripts --ignore-platform-reqs \
 && rm -rf composer*

FROM xigen/php:cli-ppm-edge

ENV APP_ENV=prod \
  POSTGRES_DB=medleybox_webapp \
  POSTGRES_USER=medleybox \
  POSTGRES_PASSWORD='' \
  PPM_PORT=8080 \
  REDIS_URL='redis://redis'

COPY --from=composer /app /app
COPY --from=ghcr.io/medleybox/frontend:master /app /app/public
COPY --from=ghcr.io/medleybox/encore:master /app/public/build /app/public/build
COPY bin/console /app/bin/console
COPY docker/app/php.ini /etc/php7/php.ini
COPY templates /app/templates

USER root

RUN chown -Rf 82:82 /app && apk add php7-intl

USER 82

RUN chmod +x bin/console \
  && bin/console cache:warmup -eprod --no-optional-warmers

