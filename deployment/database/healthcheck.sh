#!/bin/bash
# deployment/scripts/mariadb-healthcheck.sh

set -e

MARIADB_HOST="${MARIADB_HOST:-localhost}"
MARIADB_PORT="${MARIADB_PORT:-3306}"
MARIADB_USER="${MARIADB_USER:-migrations}"
MARIADB_PASSWORD="${MARIADB_PASSWORD:-}"
MARIADB_DATABASE="${MARIADB_DATABASE:-reservations}"

if [ -z "$MARIADB_PASSWORD" ]; then
    echo "Error: MARIADB_PASSWORD environment variable is required" >&2
    exit 1
fi

# Test database connectivity
if ! mariadb -h"${MARIADB_HOST}" -P"${MARIADB_PORT}" -u"${MARIADB_USER}" -p"${MARIADB_PASSWORD}" -e "SELECT 1;" &> /dev/null; then
    echo "Error: Failed to connect to MariaDB" >&2
    exit 1
fi

# Check if migrations database exists
db_exists=$(mariadb -h"${MARIADB_HOST}" -P"${MARIADB_PORT}" -u"${MARIADB_USER}" -p"${MARIADB_PASSWORD}" \
    -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '${MARIADB_DATABASE}';" \
    --skip-column-names --silent 2>/dev/null || echo "")

if [ -z "$db_exists" ] || [ "$db_exists" != "${MARIADB_DATABASE}" ]; then
    echo "Error: Migrations database '${MARIADB_DATABASE}' not found" >&2
    exit 1
fi

exit 0
