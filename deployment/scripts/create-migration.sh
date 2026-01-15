#!/bin/bash
# deployment/scripts/create-migration.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
MIGRATIONS_DIR="$PROJECT_ROOT/src/Infrastructure/Ports/Migrations/migrations"

# Generate timestamp in YYYYMMDDhhmmss format
TIMESTAMP=$(date +%Y%m%d%H%M%S)

# Create migration directory
MIGRATION_DIR="$MIGRATIONS_DIR/$TIMESTAMP"
mkdir -p "$MIGRATION_DIR"

# Create migration file
MIGRATION_FILE="$MIGRATION_DIR/0001_migration.sql"
cat > "$MIGRATION_FILE" << 'EOF'
-- Migration file
USE reservations;
EOF

# Create rollback file
ROLLBACK_FILE="$MIGRATION_DIR/0001_migration.rollback.sql"
cat > "$ROLLBACK_FILE" << 'EOF'
-- Rollback file
USE reservations;
EOF

echo "✅ Migration created successfully!"
echo "   Directory: $MIGRATION_DIR"
echo "   Migration file: $MIGRATION_FILE"
echo "   Rollback file: $ROLLBACK_FILE"
