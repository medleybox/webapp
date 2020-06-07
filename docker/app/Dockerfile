FROM xigen/php:cli-composer74 as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install --no-dev -o -a --no-scripts --ignore-platform-reqs

FROM xigen/php:cli-ppm

ENV APP_ENV=prod \
  POSTGRES_DB=medleybox_webapp

COPY --from=composer /app/vendor /app/vendor
COPY . /app
COPY --from=medleybox/frontend:latest /app/dist /app/public

RUN ls -alsh /app/public