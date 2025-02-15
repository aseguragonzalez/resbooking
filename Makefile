.PHONY: all test fix lint analyse

all: test fix lint analyse

install:
	@pre-commit install
	@composer install

update-autoload:
	@composer dump-autoload

test:
	@./vendor/bin/phpunit  --configuration=./tests/phpunit.xml ./tests/

fix:
	@./vendor/bin/php-cs-fixer fix .

lint:
	@./vendor/bin/phpcs --standard=PSR12 src/

analyse:
	@./vendor/bin/phpstan analyse ./src
