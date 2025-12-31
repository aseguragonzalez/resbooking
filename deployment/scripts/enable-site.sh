#!/bin/bash
set -e

if [ -z "$1" ]; then
    echo "Usage: $0 <webapp-name>"
    echo "Example: $0 dashboard"
    exit 1
fi

WEBAPP_NAME=$1
APP_DIR="/var/www/html/deployment/apps/${WEBAPP_NAME}"
VHOST_CONFIG="${APP_DIR}/vhost.conf"
ENVVARS_CONFIG="${APP_DIR}/envvars.conf"

if [ ! -d "$APP_DIR" ]; then
    echo "Error: App directory not found: $APP_DIR"
    exit 1
fi

if [ ! -f "$VHOST_CONFIG" ]; then
    echo "Error: VirtualHost configuration not found: $VHOST_CONFIG"
    exit 1
fi

echo "Enabling site: $WEBAPP_NAME"

# Copy VirtualHost configuration
cp "$VHOST_CONFIG" "/etc/apache2/sites-available/${WEBAPP_NAME}.conf"

# Copy environment variables if they exist
if [ -f "$ENVVARS_CONFIG" ]; then
    mkdir -p /etc/apache2/conf-available/envvars
    cp "$ENVVARS_CONFIG" "/etc/apache2/conf-available/envvars/${WEBAPP_NAME}.envvars.conf"
    echo "  - Environment variables configured"
fi

# Enable the site
a2ensite "${WEBAPP_NAME}.conf"
apache2ctl graceful

echo "Site $WEBAPP_NAME enabled and Apache reloaded"
