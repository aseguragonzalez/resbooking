.PHONY: all format format-check lint static-analyse test install clean update-autoload create-package

all: format-check lint static-analyse test

clean:
	@rm -rf vendor
	@rm -rf coverage
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache
	@rm -rf dist

format:
	@if [ -n "$$(find ./src ./tests -name '*.php' 2>/dev/null)" ]; then ./vendor/bin/php-cs-fixer fix .; else echo "No PHP files to format."; fi

format-check:
	@if [ -n "$$(find ./src ./tests -name '*.php' 2>/dev/null)" ]; then ./vendor/bin/php-cs-fixer fix . --dry-run --diff; else echo "No PHP files to check."; fi

install:
	@composer install
	@export PATH=$$PATH:./vendor/bin

lint:
	@if [ -n "$$(find ./src ./tests -name '*.php' 2>/dev/null)" ]; then ./vendor/bin/phpcs --standard=PSR12 ./src ./tests; else echo "No PHP files to lint."; fi

static-analyse:
	@if [ -n "$$(find ./src ./tests -name '*.php' 2>/dev/null)" ]; then ./vendor/bin/phpstan analyse ./src ./tests --level=max --memory-limit=1G; else echo "No PHP files to analyse."; fi

test:
	@./vendor/bin/phpunit -c phpunit.xml

update-autoload:
	@composer dump-autoload

create-package:
	@mkdir -p dist
	@composer archive --format=zip --dir=dist
