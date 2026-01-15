#!/bin/bash
# deployment/scripts/add-migration-file.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
MIGRATIONS_DIR="$PROJECT_ROOT/src/Infrastructure/Ports/Migrations/migrations"

# Get folder parameter (optional)
FOLDER="$1"

# Determine target folder
if [ -z "$FOLDER" ]; then
    # Find the most recent migration folder
    if [ ! -d "$MIGRATIONS_DIR" ]; then
        echo "❌ Error: Migrations directory does not exist: $MIGRATIONS_DIR"
        exit 1
    fi

    # Find latest folder by sorting timestamps (numeric sort for proper ordering)
    FOLDER=$(ls -1 "$MIGRATIONS_DIR" | grep -E '^[0-9]+' | sort -n | tail -n1)

    if [ -z "$FOLDER" ]; then
        echo "❌ Error: No migration folders found in $MIGRATIONS_DIR"
        echo "   Please create a migration folder first using: make create-migration"
        exit 1
    fi
fi

# Validate folder exists
MIGRATION_DIR="$MIGRATIONS_DIR/$FOLDER"
if [ ! -d "$MIGRATION_DIR" ]; then
    echo "❌ Error: Migration folder does not exist: $MIGRATION_DIR"
    exit 1
fi

# Find the highest numbered file
MAX_NUM=0
if ls "$MIGRATION_DIR"/*_migration.sql 1> /dev/null 2>&1; then
    for file in "$MIGRATION_DIR"/*_migration.sql; do
        filename=$(basename "$file")
        # Extract number from filename (e.g., 0001_migration.sql -> 0001)
        num=$(echo "$filename" | sed -E 's/^([0-9]+)_.*/\1/' | sed 's/^0*//')
        # Handle case where num is empty (all zeros)
        if [ -z "$num" ]; then
            num=0
        fi
        if [ "$num" -gt "$MAX_NUM" ]; then
            MAX_NUM=$num
        fi
    done
fi

# Increment the number
NEXT_NUM=$((MAX_NUM + 1))
# Format with zero-padding (4 digits)
NEXT_NUM_FORMATTED=$(printf "%04d" "$NEXT_NUM")

# Create migration file
MIGRATION_FILE="$MIGRATION_DIR/${NEXT_NUM_FORMATTED}_migration.sql"
cat > "$MIGRATION_FILE" << 'EOF'
-- Migration file
USE reservations;
EOF

# Create rollback file
ROLLBACK_FILE="$MIGRATION_DIR/${NEXT_NUM_FORMATTED}_migration.rollback.sql"
cat > "$ROLLBACK_FILE" << 'EOF'
-- Rollback file
USE reservations;
EOF

echo "✅ Migration file added successfully!"
echo "   Directory: $MIGRATION_DIR"
echo "   Migration file: $MIGRATION_FILE"
echo "   Rollback file: $ROLLBACK_FILE"
