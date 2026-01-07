# MariaDB Deployment Configuration

This directory contains configuration files and initialization scripts for the
 MariaDB database service.

## Directory Structure

```text
deployment/database/
├── .env.example          # Environment variable template
├── init/                 # SQL initialization scripts
│   └── 01-init.sql      # Migration database initialization script
└── README.md            # This file
```

## Environment Variables

The MariaDB service uses the following environment variables (defined in your
 `.env` file):

- `MARIADB_ROOT_PASSWORD` - Root password for MariaDB
- `MARIADB_DATABASE` - Migrations database name to create (default: `migrations`)
- `MARIADB_USER` - Migrations database user (default: `migrations`)
- `MARIADB_PASSWORD` - Migrations database user password
- `MARIADB_HOST` - Service hostname (default: `mariadb`)

Migrations database is used to handle the applications database migrations.

## Initialization Scripts

SQL scripts in the `init/` directory are executed automatically when the MariaDB
 container is first created. Scripts are executed in alphabetical order.

### Script Execution Order

1. Scripts are executed only on the first container startup
2. Scripts run after the database is created but before the service becomes available
3. If you need multiple scripts, name them with numeric prefixes
 (e.g., `01-init.sql`, `02-seed-data.sql`)

### Current Scripts

- `01-init.sql` - Creates the database and sets up user permissions

## Connection Examples

### From Command Line (Inside Container)

```bash
# Connect as root
docker-compose exec mariadb mysql -u root -p

# Connect as application user
docker-compose exec mariadb mysql -u migrations -p migrations
```

### From Host Machine

```bash
# Connect via forwarded port (if configured in devcontainer)
mysql -h 127.0.0.1 -P 3306 -u migrations -p migrations
```
