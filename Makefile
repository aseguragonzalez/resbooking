.PHONY: all format lint static-analyse test setup-ssl setup-ssl-all css-build css-watch js-build js-watch watch migrate migrate-down migrate-status create-migration add-migration-file

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

# CSS Build
css-build:
	@php src/Infrastructure/Ports/Dashboard/build-css.php

css-watch:
	@php src/Infrastructure/Ports/Dashboard/build-css.php watch

# JavaScript Build
js-build:
	@php src/Infrastructure/Ports/Dashboard/build-js.php

js-watch:
	@php src/Infrastructure/Ports/Dashboard/build-js.php watch

# Combined watchers for development
watch:
	@echo "🚀 Starting CSS and JavaScript watchers..."
	@echo "Press Ctrl+C to stop both watchers\n"
	@trap 'kill 0' EXIT; \
	make css-watch & \
	make js-watch & \
	wait

# Database Migrations
migrate:
	@php src/Infrastructure/Ports/Migrations/index.php

create-migration:
	@bash deployment/scripts/create-migration.sh

# Add migration file to existing migration folder
# Usage: make add-migration-file (uses latest folder)
#        make add-migration-file FOLDER=20260115081115 (uses specified folder)
add-migration-file:
	@bash deployment/scripts/add-migration-file.sh $(FOLDER)
