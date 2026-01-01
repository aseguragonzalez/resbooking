.PHONY: all format lint static-analyse test

all: format lint static-analyse test

clean:
	@rm -rf vendor
	@rm -rf coverage
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache

format:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12

format-check:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12 --dry-run --diff

install:
	@pre-commit install
	@composer install
	@export PATH=$PATH:./vendor/bin

lint:
	@./vendor/bin/phpcs --standard=PSR12 ./src ./tests

static-analyse:
	@rm -rf /tmp/phpstan/cache
	@./vendor/bin/phpstan analyse ./src ./tests --level=max --memory-limit=512M

test:
	@./vendor/bin/phpunit -c phpunit.xml --coverage-html coverage/

update-autoload:
	@composer dump-autoload
