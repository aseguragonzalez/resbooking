.PHONY: all test fix lint analyse

all: test fix lint analyse

install:
	@pre-commit install
	@composer install

update-autoload:
	@composer dump-autoload

test:
	@./vendor/bin/phpunit

fix:
	@./vendor/bin/php-cs-fixer fix . --rules=@PSR12

lint:
	@./vendor/bin/phpcs --standard=PSR12 ./src ./tests

analyse:
	@./vendor/bin/phpstan analyse ./src

clean:
	@rm -rf vendor
	@rm -rf composer.lock

clean-cache:
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache
