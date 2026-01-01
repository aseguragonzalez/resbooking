# Resbooking

A simple, self-hosted restaurant reservation system that helps restaurants
manage online and phone reservations. Originally built as an MVP, this project
has been refactored to use modern PHP best practices and is now open source.

## Why This Project?

Over ten years ago, I built this project as an MVP while working on a startup
idea. It was my first attempt at solving a real-world problem: helping
restaurants manage their reservations efficiently.

Although the startup didn't take off, this project remained important to me.
Looking back, I saw an opportunity to improve it—upgrading it from `PHP 5.5` to
`PHP 8.4`, applying modern best practices, and making it cleaner and easier
to maintain.

Now, I want to give it a second life by sharing it as an open-source project.
Hopefully, others can use, learn from, and contribute to it. Whether you're a
restaurant owner looking for a simple reservation system or a developer
exploring PHP, I hope this project helps you in some way!

### Features

- **Reservation System** – Customers can book tables online.
- **Restaurant Configuration** – Set up tables, capacity, and available slots
per day and shift.
- **Email Notifications** – Send confirmations and reminders to customers.
- ~~**Promotions & Offers** – Restaurants can publish special offers based on
the day and shift.~~

### Tech Stack

- **Backend:** PHP 8.4 (upgraded from PHP 5.5) (MVC architecture over Apache)
- **Database:** MySQL
- **Frontend:** Javascript, HTML5, CSS3

## Requirements

- [Docker][docker]
- [VS Code][vscode]
- [Dev Containers Extension][devcontainers]

## Environment Variables

Before starting, you need to create a `.env` file with the environment variables
 for each application. You can use the `.env.example` file as a template.

```env
DASHBOARD_DATABASE_HOST=mysql
DASHBOARD_DATABASE_NAME=dashboard
DASHBOARD_DATABASE_PASSWORD=********
DASHBOARD_DATABASE_USER=dashboard
DASHBOARD_DEFAULT_HOST=http://dashboard
DASHBOARD_SERVICE_NAME=dashboard
DASHBOARD_SERVICE_VERSION=0.1.0
ENVIRONMENT=local
XDEBUG_MODE=coverage,debug,develop
```

## Getting Started

Open the project in VS Code or Cursor (with the Dev Containers extension) and
 wait for the Dev Container to build. Common tasks are automated using `Makefile`.

### Make commands

**Install dependencies:**

Install dependencies and pre-commit checks:

```shell
make install
```

**Clean up:**

Remove dependencies and build artifacts:

```shell
make clean
```

**Run tests:**

```shell
make test
```

**Format code:**

Fix formatting issues:

```shell
make format
```

or check formatting issues:

```shell
make format-check
```

**Check for linting issues:**

```shell
make lint
```

**Run static analysis:**

```shell
make static-analyse
```

**Update autoload:**

```shell
make update-autoload
```

### How to manage the Apache server

**Disable site:**

```shell
bash deployment/scripts/disable-site.sh <webapp-name>
```

**Enable site:**

```shell
bash deployment/scripts/enable-site.sh <webapp-name>
```

**Reload Apache:**

```shell
apache2ctl graceful
```

**Restart Apache:**

```shell
apache2ctl restart
```

**Check Apache logs:**

Check the access logs for the specific webapp:

```shell
tail -f /var/log/apache2/<webapp-name>-access.log
```

or check the error logs:

```shell
tail -f /var/log/apache2/<webapp-name>-error.log
```

### How to debug Xdebug

1. Set the Xdebug mode (`XDEBUG_MODE`) to `debug,coverage,develop` in the `.env`.
 It is set to `coverage,debug,develop` by default.
2. Install the Xdebug extension for your browser. [Chrome Xdebug][chrome-xdebug]

## Built With

- [Apache][apache]
- [Composer][composer]
- [PHP][php]
- [PHP Code Sniffer][php-cs]
- [PHP Coding Standards Fixer][php-cs-fixer]
- [PHPStan][php-stan]
- [PHPUnit][php-unit]
- [PHPUnit Test Explorer][vscode-phpunit]
- [Pre-commit][pre-commit]
- [Xdebug][xdebug]

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

[apache]: https://httpd.apache.org/
[chrome-xdebug]: https://chromewebstore.google.com/detail/oiofkammbajfehgpleginfomeppgnglk?utm_source=item-share-cb
[docker]: https://www.docker.com/
[vscode]: https://code.visualstudio.com/
[devcontainers]: https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers
[composer]: https://getcomposer.org/
[php]: https://www.php.net/
[php-cs]: https://github.com/PHPCSStandards/PHP_CodeSniffer/
[php-cs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[php-stan]: https://phpstan.org/user-guide/getting-started
[php-unit]: https://phpunit.de/index.html
[pre-commit]: https://pre-commit.com/
[vscode-phpunit]: https://marketplace.visualstudio.com/items?itemName=recca0120.vscode-phpunit
[xdebug]: https://xdebug.org/
