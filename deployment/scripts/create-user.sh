#!/bin/bash
# deployment/scripts/create-user.sh

set -e
set -o pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
DB_ENV_FILE="$PROJECT_ROOT/deployment/database/.env"

# Check if USER parameter is provided
if [ -z "$1" ]; then
    echo "❌ Error: USER parameter is required"
    echo "Usage: $0 <username>"
    echo "Example: $0 myuser"
    exit 1
fi

USERNAME="$1"

# Validate username format (alphanumeric and underscore only)
if ! [[ "$USERNAME" =~ ^[a-zA-Z0-9_]+$ ]]; then
    echo "❌ Error: Username must contain only alphanumeric characters and underscores"
    exit 1
fi

# Check if .env file exists
if [ ! -f "$DB_ENV_FILE" ]; then
    echo "❌ Error: Database environment file not found: $DB_ENV_FILE"
    echo "Please create the file using deployment/database/.env.example as a template"
    exit 1
fi

# Read database configuration from .env file
# Source the file and export variables
set -a
source "$DB_ENV_FILE"
set +a

# Check if MARIADB_ROOT_PASSWORD is set
if [ -z "$MARIADB_ROOT_PASSWORD" ]; then
    echo "❌ Error: MARIADB_ROOT_PASSWORD is not set in $DB_ENV_FILE"
    exit 1
fi

# Set default values if not provided
DB_HOST="${MARIADB_REMOTE_HOST:-mariadb}"
DB_PORT="${MARIADB_PORT:-3306}"
DB_DATABASE="${MARIADB_DATABASE:-reservations}"

# Check if mariadb or mysql client is available
if command -v mariadb &> /dev/null; then
    MYSQL_CMD="mariadb"
elif command -v mysql &> /dev/null; then
    MYSQL_CMD="mysql"
else
    echo "❌ Error: MariaDB/MySQL client is not installed"
    echo "Please install mariadb-client or mysql-client"
    exit 1
fi

# Generate a secure random password (32 characters, base64 encoded)
PASSWORD=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-32)

# Create SQL commands
SQL_COMMANDS=$(cat <<EOF
CREATE USER IF NOT EXISTS '${USERNAME}'@'%' IDENTIFIED BY '${PASSWORD}';
GRANT SELECT, INSERT, UPDATE, DELETE ON ${DB_DATABASE}.* TO '${USERNAME}'@'%';
FLUSH PRIVILEGES;
EOF
)

# Execute SQL commands using mariadb/mysql client
if ! echo "$SQL_COMMANDS" | $MYSQL_CMD \
    -h"$DB_HOST" \
    -P"$DB_PORT" \
    -uroot \
    -p"$MARIADB_ROOT_PASSWORD" \
    --protocol=TCP \
    2>&1; then
    echo "❌ Error: Failed to create user"
    echo "   Please check:"
    echo "   - MariaDB container is running (docker-compose up -d mariadb)"
    echo "   - Database is accessible at $DB_HOST:$DB_PORT"
    echo "   - Root password is correct in $DB_ENV_FILE"
    exit 1
fi

# If we get here, the command was successful
echo ""
echo "✅ User created successfully!"
echo "   Username: $USERNAME"
echo "   Password: $PASSWORD"
echo "   Database: $DB_DATABASE"
echo "   Privileges: SELECT, INSERT, UPDATE, DELETE"
echo ""
echo "⚠️  Important: Save this password now. It will not be shown again."
