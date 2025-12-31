.PHONY: all format lint static-analyse test

all: format lint static-analyse test

apache-restart:
	@apache2ctl restart

apache-reload:
	@apache2ctl graceful

apache-logs:
	@tail -f /var/log/apache2/*.log

clean:
	@rm -rf vendor
	@rm -rf coverage
	@rm -rf .phpunit.cache
	@rm -rf .php-cs-fixer.cache
	@rm -rf coverage

disable-site:
	@if [ -z "$(WEBAPP)"; then \
		echo "Usage: make disable-site WEBAPP=dashboard|coverage"; \
		exit 1; \
	fi
	@bash deployment/scripts/disable-site.sh $(WEBAPP)

enable-site:
	@if [ -z "$(WEBAPP)"; then \
		echo "Usage: make enable-site WEBAPP=dashboard|coverage"; \
		exit 1; \
	fi
	@bash deployment/scripts/enable-site.sh $(WEBAPP)

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
	@./vendor/bin/phpunit --coverage-html coverage/

update-autoload:
	@composer dump-autoload
