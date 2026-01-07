-- MariaDB Initialization Script
-- This script runs automatically when the MariaDB container is first created
-- Scripts in this directory are executed in alphabetical order

-- Create the database if it doesn't exist
-- Note: MARIADB_DATABASE environment variable will create the database automatically,
-- but this ensures it exists even if the env var is not set
CREATE DATABASE IF NOT EXISTS migrations CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the application user
-- Note: MARIADB_USER and MARIADB_PASSWORD environment variables will create the user automatically,
-- but this ensures proper permissions are set
GRANT ALL PRIVILEGES ON migrations.* TO 'migrations'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;

-- You can add additional initialization SQL here, such as:
-- - Creating initial tables
-- - Inserting seed data
-- - Setting up indexes
-- - Creating views or stored procedures
