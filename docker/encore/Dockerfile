FROM node:16-alpine3.12 as builder

RUN apk add --no-cache alpine-sdk python3

WORKDIR /app

COPY assets /app/assets
COPY package.json webpack.config.js package-lock.json /app/

RUN mkdir -p /app/public/build && \
  npm install && \
  ./node_modules/.bin/encore production

# Final image
FROM node:16-alpine3.12 as final

WORKDIR /app

COPY --from=builder /app /app

ENTRYPOINT ["node", "./node_modules/.bin/encore", "dev-server", "--host=medleybox.local", "--port=443", "--keep-public-path", "--https"]