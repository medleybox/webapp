# Webapp
[![Docker Hub Link](https://img.shields.io/docker/image-size/medleybox/webapp/latest?style=for-the-badge)][dockerhub-webapp]
[![Docker Hub Link](https://img.shields.io/docker/cloud/automated/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]
[![Docker Hub Link](https://img.shields.io/docker/cloud/build/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]

A Symfony 4 application to serve the frontend of Medleybox.

## Quick Start
```bash
# Install PHP dependencies on host
composer install

# Build docker images locally
bin/docker-build

# Start docker containers and run doctrine migrations
bin/docker-up

# Create the first admin user
bin/docker-console app:create-user --username admin
```

Once the user has been created, you can login via https://localhost

[dockerhub-webapp]: https://hub.docker.com/repository/docker/medleybox/webapp
[dockerhub-webapp-builds]: https://hub.docker.com/repository/docker/medleybox/webapp/builds