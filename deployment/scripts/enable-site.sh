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

# Check if SSL certificate files exist (SSL is mandatory)
CERT_FILE="/etc/apache2/ssl/${WEBAPP_NAME}.crt"
KEY_FILE="/etc/apache2/ssl/${WEBAPP_NAME}.key"

if [ ! -f "$CERT_FILE" ] || [ ! -f "$KEY_FILE" ]; then
    echo "❌ Error: SSL certificate files not found"
    echo "   Expected: $CERT_FILE"
    echo "   Expected: $KEY_FILE"
    echo ""
    echo "SSL is mandatory. Please generate the certificate first:"
    echo "  bash deployment/scripts/generate-ssl-cert.sh $WEBAPP_NAME"
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
apache2ctl configtest && apache2ctl graceful

echo "Site $WEBAPP_NAME enabled and Apache reloaded"
