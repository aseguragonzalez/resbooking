.PHONY: all format lint static-analyse test setup-ssl setup-ssl-all

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
	@./vendor/bin/phpstan analyse ./src ./tests --level=max --memory-limit=1G

test:
	@./vendor/bin/phpunit -c phpunit.xml --coverage-html coverage/

update-autoload:
	@composer dump-autoload

# Setup SSL certificate for a specific app (SSL is mandatory)
# Usage: make setup-ssl APP=dashboard
setup-ssl:
	@if [ -z "$(APP)" ]; then \
		echo "❌ Error: APP parameter is required"; \
		echo "Usage: make setup-ssl APP=<app-name>"; \
		echo "Example: make setup-ssl APP=dashboard"; \
		exit 1; \
	fi
	@bash deployment/scripts/generate-ssl-cert.sh $(APP)

# Setup SSL certificates for all apps (SSL is mandatory)
setup-ssl-all:
	@echo "🔐 Generating SSL certificates for all apps (SSL is mandatory)..."
	@for app in deployment/apps/*/; do \
		app_name=$$(basename $$app); \
		echo ""; \
		echo "📦 Processing app: $$app_name"; \
		bash deployment/scripts/generate-ssl-cert.sh $$app_name || true; \
	done
	@echo ""
	@echo "✅ SSL certificate generation complete!"
