FROM xigen/php:cli-composer as composer

COPY composer* /app/
COPY config/ /app/config
COPY src/ /app/src

RUN composer install --no-dev -o -a --no-scripts --ignore-platform-reqs \
 && du -sh vendor/ \
 && rm -rf composer*

FROM xigen/php:cli-ppm

ENV APP_ENV=prod \
  POSTGRES_DB=medleybox_webapp \
  REDIS_URL='redis://redis'

COPY --from=composer /app /app
#COPY . /app
COPY --from=medleybox/frontend:latest /app /app/public

RUN ls -alsh /app