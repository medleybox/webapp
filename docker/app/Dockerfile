FROM xigen/php:cli-composer74 as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install -vvv -o -a --no-scripts --ignore-platform-reqs

FROM xigen/php:cli-ppm

COPY --from=composer /app/vendor /var/www/vendor
COPY . /app