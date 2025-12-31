#!/bin/bash
set -e

if [ -z "$1" ]; then
    echo "Usage: $0 <webapp-name>"
    echo "Example: $0 dashboard"
    exit 1
fi

WEBAPP_NAME=$1

echo "Disabling site: $WEBAPP_NAME"
a2dissite "${WEBAPP_NAME}.conf" 2>/dev/null || true
apache2ctl graceful

echo "Site $WEBAPP_NAME disabled and Apache reloaded"
