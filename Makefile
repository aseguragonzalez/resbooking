.PHONY: all test fix lint analyse

all: test fix lint analyse

install:
	@pre-commit install
	@composer install
	@export PATH=$PATH:./vendor/bin
	@export XDEBUG_MODE=coverage

update-autoload:
	@composer dump-autoload

test:
	@XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage/

fix:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12

lint:
	@./vendor/bin/phpcs --standard=PSR12 ./src ./tests

analyse:
	@./vendor/bin/phpstan analyse ./src ./tests --level=max

clean:
	@rm -rf vendor
	@rm -rf coverage

clean-cache:
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache

clean-coverage:
	@rm -rf coverage

open-coverage:
	cd coverage && php -S 0.0.0.0:9000
