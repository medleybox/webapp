FROM node:18-alpine3.16 as builder

RUN apk add --no-cache alpine-sdk python3

WORKDIR /app

COPY package.json webpack.config.js package-lock.json /app/

RUN mkdir -p /app/public/build && npm install

COPY assets /app/assets

RUN node ./node_modules/.bin/encore production

# Final image
FROM node:18-alpine3.16 as final

ENV ENCORE_HOST medleybox.local

WORKDIR /app

COPY --from=builder /app /app
COPY docker/encore/entrypoint /bin/docker-entrypoint

ENTRYPOINT ["/bin/docker-entrypoint"]