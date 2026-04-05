.PHONY: all format lint static-analyse test setup-ssl setup-ssl-all css-build css-watch js-build js-watch watch migrate migrate-down migrate-status create-migration add-migration-file test-migration create-user background-tasks env

# Load .env so all make targets (and their commands) get these variables in any terminal
ifneq (,$(wildcard .env))
include .env
export
endif

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
	@./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G

test:
	@./vendor/bin/phpunit -c phpunit.xml --coverage-html coverage/

update-autoload:
	@composer dump-autoload

# Print export statements to refresh the current terminal only (run: eval $$(make env))
env:
	@grep -v '^#' .env 2>/dev/null | grep -v '^$$' | sed 's/^/export /'

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

# Dashboard asset bundles (see src/Infrastructure/Ports/Dashboard/mvc.config.json)
DASHBOARD_APP_PATH := src/Infrastructure/Ports/Dashboard

# CSS / JS build (minified bundles; same as mvc create-bundle for the Dashboard app)
css-build:
	@php src/Framework/Cli/bin/mvc create-bundle --app-path=$(DASHBOARD_APP_PATH)

js-build:
	@php src/Framework/Cli/bin/mvc create-bundle --app-path=$(DASHBOARD_APP_PATH)

# Watch unminified bundles (JS + CSS in one process; same as mvc watch-assets)
css-watch:
	@php src/Framework/Cli/bin/mvc watch-assets --app-path=$(DASHBOARD_APP_PATH)

js-watch:
	@php src/Framework/Cli/bin/mvc watch-assets --app-path=$(DASHBOARD_APP_PATH)

watch:
	@echo "🚀 Watching Dashboard JS and CSS (mvc watch-assets)..."
	@echo "Press Ctrl+C to stop\n"
	@php src/Framework/Cli/bin/mvc watch-assets --app-path=$(DASHBOARD_APP_PATH)

# Run background tasks (processes pending email and other queued tasks)
background-tasks:
	@php src/Infrastructure/Ports/BackgroundTasks/index.php


# Create a new database user with read/write access to reservations database
# Usage: make create-user USER=<username>
create-user:
	@if [ -z "$(USER)" ]; then \
		echo "❌ Error: USER parameter is required"; \
		echo "Usage: make create-user USER=<username>"; \
		echo "Example: make create-user USER=myuser"; \
		exit 1; \
	fi
	@bash deployment/scripts/create-user.sh $(USER)
