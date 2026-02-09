-- MariaDB Initialization Script
-- This script runs automatically when the MariaDB container is first created
-- Scripts in this directory are executed in alphabetical order

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS reservations CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the application user
GRANT ALL PRIVILEGES ON reservations.* TO 'migrations'@'%';

-- Allow creating/dropping temporary databases and foreign keys for migration tests (e.g. test_xxxx)
GRANT CREATE, DROP, REFERENCES ON *.* TO 'migrations'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;

USE reservations;

CREATE TABLE IF NOT EXISTS migrations_history (
    migration VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (migration, filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
