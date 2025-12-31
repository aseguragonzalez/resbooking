#!/bin/bash
set -e

if [ -z "$1" ]; then
    echo "Usage: $0 <webapp-name>"
    echo "Example: $0 dashboard"
    exit 1
fi

WEBAPP_NAME=$1
SITE_CONFIG="/var/www/html/deployment/apache/sites-available/${WEBAPP_NAME}.conf"

if [ ! -f "$SITE_CONFIG" ]; then
    echo "Error: Site configuration not found: $SITE_CONFIG"
    exit 1
fi

echo "Enabling site: $WEBAPP_NAME"
cp "$SITE_CONFIG" "/etc/apache2/sites-available/${WEBAPP_NAME}.conf"
a2ensite "${WEBAPP_NAME}.conf"
apache2ctl graceful

echo "Site $WEBAPP_NAME enabled and Apache reloaded"
