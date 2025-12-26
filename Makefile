.PHONY: all format lint static-analyse test

all: format lint static-analyse test

install:
	@pre-commit install
	@composer install
	@export PATH=$PATH:./vendor/bin
	@export XDEBUG_MODE=coverage,debug,develop

clean:
	@rm -rf vendor
	@rm -rf coverage
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache
	@rm -rf coverage

format:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12

format-check:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12 --dry-run --diff

lint:
	@./vendor/bin/phpcs --standard=PSR12 ./src ./tests

static-analyse:
	@./vendor/bin/phpstan analyse ./src ./tests --level=max --memory-limit=512M

serve:
	@php -S 0.0.0.0:8080 src/Infrastructure/Ports/Dashboard/local.php 2> php-server.log

open-coverage:
	cd coverage && php -S 0.0.0.0:9000

test:
	@./vendor/bin/phpunit --coverage-html coverage/

update-autoload:
	@composer dump-autoload
