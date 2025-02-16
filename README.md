# Resbooking WebApp

## Makefile Commands

The following commands are available in the `Makefile`:

Install dependencies

```shell
make install
```

Run tests

```shell
make test
```

Run PHP CS Fixer

```shell
make fix
```

Run PHP CodeSniffer

```shell
make lint
```

Run PHPStan

```shell
make analyse
```

## How to

Install dependencies

```shell
composer install
```

Recreate autoload

```shell
composer dump-autoload
```

Run tests

```shell
php ./vendor/bin/phpunit --configuration=./tests/phpunit.xml ./tests/
```

Run PHP tools

PHP CS Fixer

```shell
./vendor/bin/php-cs-fixer fix .
```

PHP CodeSniffer

```shell
./vendor/bin/phpcs --standard=PSR12 ./src ./tests
```

PHPStan

```shell
./vendor/bin/phpstan analyse ./src ./tests --level=max
```

## Build With

- [Composer][composer]
- [PHP][php]
- [PHP Code Sniffer][php-cs]
- [PHP Coding Standards Fixer][php-cs-fixer]
- [PHPStan][php-stan]
- [PHPUnit][php-unit]

[composer]: https://getcomposer.org/
[php]: https://www.php.net/
[php-cs]: https://github.com/PHPCSStandards/PHP_CodeSniffer/
[php-cs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[php-stan]: https://phpstan.org/user-guide/getting-started
[php-unit]: https://phpunit.de/index.html
