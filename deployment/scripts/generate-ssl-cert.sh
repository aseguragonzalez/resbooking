#!/bin/bash
# deployment/scripts/generate-ssl-cert.sh

set -e

# Usage function
usage() {
    echo "Usage: $0 <app-name>"
    echo ""
    echo "Generate SSL certificate for a specific webapp."
    echo ""
    echo "Arguments:"
    echo "  app-name    Name of the webapp (e.g., dashboard, coverage)"
    echo ""
    echo "Examples:"
    echo "  $0 dashboard"
    echo "  $0 coverage"
    exit 1
}

# Check if app name is provided
if [ -z "$1" ]; then
    echo "❌ Error: App name is required"
    echo ""
    usage
fi

APP_NAME="$1"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
SSL_DIR="$PROJECT_ROOT/deployment/ssl"
APPS_DIR="$PROJECT_ROOT/deployment/apps"
VHOST_CONF="$APPS_DIR/$APP_NAME/vhost.conf"

# Validate app exists
if [ ! -d "$APPS_DIR/$APP_NAME" ]; then
    echo "❌ Error: App '$APP_NAME' not found in $APPS_DIR"
    echo ""
    echo "Available apps:"
    ls -1 "$APPS_DIR" 2>/dev/null | sed 's/^/  - /' || echo "  (none found)"
    exit 1
fi

# Validate vhost.conf exists
if [ ! -f "$VHOST_CONF" ]; then
    echo "❌ Error: VirtualHost configuration not found: $VHOST_CONF"
    exit 1
fi

# Check if mkcert is installed
if ! command -v mkcert &> /dev/null; then
    echo "❌ Error: mkcert is not installed"
    echo ""
    echo "Please install mkcert first:"
    echo "  macOS:   brew install mkcert"
    echo "  Linux:   See https://github.com/FiloSottile/mkcert#installation"
    echo "  Windows: choco install mkcert"
    exit 1
fi

# Create SSL directory
mkdir -p "$SSL_DIR"

# Install local CA (if not already installed)
if [ ! -f "$(mkcert -CAROOT)/rootCA.pem" ]; then
    echo "📜 Installing local CA (you may be prompted for your password)..."
    mkcert -install
    echo "✅ Local CA installed"
else
    echo "✅ Local CA already installed"
fi

# Extract ServerName and ServerAlias from vhost.conf
SERVER_NAME=$(grep -E "^\s*ServerName\s+" "$VHOST_CONF" | sed 's/.*ServerName\s*\([^#]*\).*/\1/' | tr -d ' ' | head -n1)
SERVER_ALIAS=$(grep -E "^\s*ServerAlias\s+" "$VHOST_CONF" | sed 's/.*ServerAlias\s*\([^#]*\).*/\1/' | tr -d ' ' | head -n1)

if [ -z "$SERVER_NAME" ]; then
    echo "❌ Error: Could not extract ServerName from $VHOST_CONF"
    exit 1
fi

# Build domain list - ONLY from ServerName and ServerAlias
# The certificate must match the Host header, which comes from ServerName/ServerAlias
DOMAINS=(
    "$SERVER_NAME"
)

# Add ServerAlias if present
if [ -n "$SERVER_ALIAS" ]; then
    # Handle wildcard aliases like "*.dashboard"
    if [[ "$SERVER_ALIAS" == *.* ]]; then
        DOMAINS+=("$SERVER_ALIAS")
        # Also add the base domain without wildcard (e.g., "dashboard" from "*.dashboard")
        BASE_DOMAIN=$(echo "$SERVER_ALIAS" | sed 's/^\*\.//')
        # Only add if it's different from SERVER_NAME
        if [[ "$BASE_DOMAIN" != "$SERVER_NAME" ]] && [[ ! " ${DOMAINS[@]} " =~ " ${BASE_DOMAIN} " ]]; then
            DOMAINS+=("$BASE_DOMAIN")
        fi
    else
        # Non-wildcard alias
        if [[ ! " ${DOMAINS[@]} " =~ " ${SERVER_ALIAS} " ]]; then
            DOMAINS+=("$SERVER_ALIAS")
        fi
    fi
fi

# Certificate file names based on app name
CERT_FILE="$SSL_DIR/${APP_NAME}.crt"
KEY_FILE="$SSL_DIR/${APP_NAME}.key"

# Check if certificate already exists
if [ -f "$CERT_FILE" ] && [ -f "$KEY_FILE" ]; then
    echo "ℹ️  Certificate already exists for $APP_NAME"
    echo "   Certificate: $CERT_FILE"
    echo "   Private Key: $KEY_FILE"
    read -p "   Do you want to regenerate it? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "✅ Using existing certificate"
        exit 0
    fi
    rm -f "$CERT_FILE" "$KEY_FILE"
fi

echo "🔨 Generating certificate for app: $APP_NAME"
echo "   ServerName: $SERVER_NAME"
if [ -n "$SERVER_ALIAS" ]; then
    echo "   ServerAlias: $SERVER_ALIAS"
fi
echo "   Certificate will be valid for: ${DOMAINS[*]}"
echo ""

cd "$SSL_DIR"

# Generate certificate with domains from ServerName/ServerAlias only
mkcert \
    -cert-file "${APP_NAME}.crt" \
    -key-file "${APP_NAME}.key" \
    "${DOMAINS[@]}"

# Set proper permissions
chmod 600 "${APP_NAME}.key"
chmod 644 "${APP_NAME}.crt"

echo ""
echo "✅ Certificate generated successfully!"
echo ""
echo "📁 Certificate location:"
echo "   Certificate: $CERT_FILE"
echo "   Private Key: $KEY_FILE"
echo ""
echo "🌐 Your browser will trust these certificates automatically"
echo ""
echo "📝 Certificate details:"
openssl x509 -in "$CERT_FILE" -text -noout | grep -A 2 "Subject:" | head -n 2
echo ""
echo "   Valid for domains:"
openssl x509 -in "$CERT_FILE" -text -noout | grep -E "DNS:|IP Address:" | sed 's/^[[:space:]]*/     /'
