FROM node:15-alpine3.12

WORKDIR /app

COPY assets /app/assets
COPY public/build /app/public/build
COPY package.json webpack.config.js package-lock.json /app/

RUN npm install && \
  ./node_modules/.bin/encore production

ENTRYPOINT ["node", "./node_modules/.bin/encore", "dev-server", "--host=medleybox.local", "--port=443", "--https", "--firewall=false"]
