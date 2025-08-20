.PHONY: all test fix lint analyse

all: test fix lint analyse

install:
	@pre-commit install
	@composer install
	@export PATH=$PATH:./vendor/bin
	@export XDEBUG_MODE=coverage

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

fix:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12

lint:
	@./vendor/bin/phpcs --standard=PSR12 ./src ./tests

open-coverage:
	cd coverage && php -S 0.0.0.0:9000

test:
	@XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage/

# Start the PHP built-in server but redirect output to a log file
serve:
	@php -S 0.0.0.0:8080 src/Infrastructure/Ports/Dashboard/local.php 2> php-server.log

update-autoload:
	@composer dump-autoload
