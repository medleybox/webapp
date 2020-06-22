# Webapp
[![Docker Hub Link](https://img.shields.io/docker/image-size/medleybox/webapp/latest?style=for-the-badge)][dockerhub-webapp]
[![Docker Hub Link](https://img.shields.io/docker/cloud/automated/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]
[![Docker Hub Link](https://img.shields.io/docker/cloud/build/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]

A Symfony 4 application to serve the frontend of Medleybox.

## Quick Start
```bash
# Install PHP dependencies on to host
composer install

# Build docker images
bin/docker-build

# Start docker containers
bin/docker-up
```

[dockerhub-webapp]: https://hub.docker.com/repository/docker/medleybox/webapp
[dockerhub-webapp-builds]: https://hub.docker.com/repository/docker/medleybox/webapp/builds