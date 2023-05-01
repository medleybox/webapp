# Webapp

A Symfony 6 application to serve the frontend of Medleybox.

[![php-composer-and-tests](https://github.com/medleybox/webapp/actions/workflows/php-composer-and-tests.yml/badge.svg)][github-workflows-tests]
[![docker-publish](https://github.com/medleybox/webapp/actions/workflows/docker-publish.yml/badge.svg)][github-workflows-publish]

## Quick Start

Add hostsfile for medleybox.local:
```
127.0.0.1 medleybox.local
```

Run the following in your terminal:
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

Once the user has been created, you can login via https://medleybox.local

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

## Useful links
Services:
- [Mailhog][medleybox-mailhog]

Docs:
- [Vue Material][vuematerial-docs]
- [Material Icons][material-icons]

[github-workflows-tests]: https://github.com/medleybox/webapp/actions/workflows/php-composer-and-tests.yml
[github-workflows-publish]: https://github.com/medleybox/webapp/actions/workflows/docker-publish.yml
[phpcs-symfony-ruleset]: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.17/doc/ruleSets/SymfonyRisky.rst
[medleybox-mailhog]: https://medleybox.local/mailhog
[vuematerial-docs]: https://www.creative-tim.com/vuematerial/components/app
[material-icons]: https://fonts.google.com/icons?icon.set=Material+Icons