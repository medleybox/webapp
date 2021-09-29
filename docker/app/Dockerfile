FROM composer:2 as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install --no-dev -o -a --no-scripts --ignore-platform-reqs \
 && rm -rf composer*

FROM phpswoole/swoole:latest-alpine

ENV IGBINARY_VERSION="3.2.6"
ENV REDIS_VERSION="5.3.4"

RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/${REDIS_VERSION}.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-* /usr/src/php/ext/redis \
    && cd /tmp \
    && wget https://github.com/igbinary/igbinary/archive/${IGBINARY_VERSION}.zip \
    && unzip ${IGBINARY_VERSION}.zip \
    && ls -alsh \
    && mv -v igbinary-* /usr/src/php/ext/igbinary \
    && apk add --no-cache --virtual .build-deps \
    autoconf \
    binutils \
    freetype-dev \
    g++ \
    git \
    icu-dev \
    icu-libs \
    libxslt-dev \
    make \
    postgresql-dev \
    && apk add --no-cache \
        freetype \
        icu \
        libxml2 \
        libxslt \
        tzdata \
        postgresql-libs \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j "$(getconf _NPROCESSORS_ONLN)" \
        intl \
        pcntl \
        opcache \
        pdo_pgsql \
        igbinary \
        redis \
        xml \
        xsl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* \
    && rm -rf /usr/src \
    && rm -rf /tmp

WORKDIR /app

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
COPY bin/entrypoint /app/bin/entrypoint
COPY docker/app/php.ini /usr/local/etc/php/conf.d/webapp.ini
COPY templates /app/templates

USER root

RUN mkdir /tmp; chown -Rf 82:82 /app /tmp

USER 82

ENTRYPOINT ["./bin/console"]
CMD ["swoole:server:run"]

RUN chmod +x bin/console

