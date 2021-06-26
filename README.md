# Webapp
[![Docker Hub Link](https://img.shields.io/docker/image-size/medleybox/webapp/latest?style=for-the-badge)][dockerhub-webapp]
[![Docker Hub Link](https://img.shields.io/docker/cloud/automated/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]
[![Docker Hub Link](https://img.shields.io/docker/cloud/build/medleybox/webapp?style=for-the-badge)][dockerhub-webapp-builds]
[![Github Workflows Link](https://github.com/medleybox/webapp/workflows/PHP%20Tests/badge.svg)][github-workflows]

A Symfony 5 application to serve the frontend of Medleybox.

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

## Bin Scripts
Bin scripts have been written to automate common tasks:

| Script | Description |
|--|--|
| bin/docker-build | Build docker images via docker-compose |
| bin/docker-console | Run the Symfony Console within the webapp container |
| bin/docker-dumpserver | Command to run Symfony Dump server |
| bin/docker-logs | Follow the stack logs via docker-compose |
| bin/docker-psql | Login to the postgresql server |
| bin/docker-push | Push locally built docker images to repository |
| bin/docker-up | Start the stack locally via docker-compose |
| bin/run-tests | Run PHP CS tests using phpstan and phpcs |


## Testing
PHP Coding Standards tests using `phpstan` and `squizlabs/php_codesniffer` using the [Symfony:risky][phpcs-symfony-ruleset] ruleset. Use the `run-tests` bin script to use the correct command line arguments for each program.

Fix reported issues with `phpcbf`:
```
vendor/bin/phpcbf --standard=PSR12 --colors src
```

[dockerhub-webapp]: https://hub.docker.com/repository/docker/medleybox/webapp
[dockerhub-webapp-builds]: https://hub.docker.com/repository/docker/medleybox/webapp/builds
[github-workflows]: https://github.com/medleybox/webapp/actions?query=workflow%3A%22PHP+Tests%22
[phpcs-symfony-ruleset]: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.17/doc/ruleSets/SymfonyRisky.rst