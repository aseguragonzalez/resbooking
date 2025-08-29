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
- **Promotions & Offers** – Restaurants can publish special offers based on
the day and shift.

### Tech Stack

- **Backend:** PHP 8.4 (upgraded from PHP 5.5)
- **Database:** MySQL
- **Frontend:** Javascript, HTML, CSS

## Requirements

- [Docker][docker]
- [VS Code][vscode]
- [Dev Containers Extension][devcontainers]

## Getting Started

Common tasks are automated using `Makefile`.

**Environment Variables:**

```sheel
export XDEBUG_MODE=coverage
```

**Install dependencies:**

```shell
make install
```

**Run tests:**

```sh
make test
```

**Check for linting issues:**

```sh
make lint
```

**Run static analysis:**

```sh
make analyze
```

## Build With

- [Composer][composer]
- [PHP][php]
- [PHP Code Sniffer][php-cs]
- [PHP Coding Standards Fixer][php-cs-fixer]
- [PHPStan][php-stan]
- [PHPUnit][php-unit]
- [Pre-commit][pre-commit]

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

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
