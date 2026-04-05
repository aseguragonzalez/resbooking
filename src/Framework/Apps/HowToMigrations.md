# How to use MVC database migrations

Migrations live under a **migration module** directory next to your app’s `index.php`. The module contains `index.php` (CLI entry), `MigrationsBootstrap.php` (environment and DI wiring for that instance), and either a nested `migrations/` folder **or** timestamped migration folders directly in the module (flat layout). Paths are configured in **`mvc.config.json`**.

## Configuration (`mvc.config.json`)

- **`migrationsFolderPath`** — Relative path from the app root to the migration module directory (contains `index.php` and usually `migrations/`). Example: `./Migrations`.
- **`migrationsEnabled`** — `true` when the feature is enabled; `false` when disabled. New apps created with `mvc create-app` start with `migrationsEnabled: false` until you enable migrations.

## Enable the feature

From the project root (or set `--path` to your MVC app directory):

```bash
mvc migrations:enable [--path=<app-dir>] [--folder=<module-folder>] [--namespace=<php-namespace>]
```

- **`--path`** — MVC app root (directory containing `index.php` and `mvc.config.json`). Default: current directory.
- **`--folder`** — Name of the migration module folder under the app root. Default: `Migrations`.
- **`--namespace`** — PHP namespace for the generated `MigrationsBootstrap` class. Default: `App\Migrations`.

This creates `<app>/<folder>/migrations/`, writes `<app>/<folder>/index.php` and `<app>/<folder>/MigrationsBootstrap.php`, and sets `migrationsFolderPath` and `migrationsEnabled: true` in `mvc.config.json`.

The alias **`mvc initialize-migrations`** runs the same logic as `migrations:enable` (deprecated).

## Disable the feature

```bash
mvc migrations:disable [--path=<app-dir>] [--remove-files] [--force]
```

This sets `migrationsEnabled: false` and clears `migrationsFolderPath`. By default **no files are deleted**.

- **`--remove-files`** — Delete the migration module directory from disk. Must be used together with **`--force`** to confirm.

## Database environment

The migration runner reads database settings from environment variables. The module’s **`MigrationsBootstrap::register()`** builds **`PDO`** and a **`MigrationsMysqlConnection`** (host, database, user, password, charset), registers Monolog, then calls **`Framework\Migrations\Dependencies::configure()`** (before `MigrationApp` runs — see the module’s `index.php` or the `mvc migrations:*` commands). For example:

- `MIGRATIONS_DATABASE_HOST`
- `MIGRATIONS_DATABASE_NAME`
- `MIGRATIONS_DATABASE_USER`
- `MIGRATIONS_DATABASE_PASSWORD`

Adjust these for your environment before running migrations.

## Daily commands

Run these from the repository root; use **`--app-path`** so the CLI resolves the migrations leaf directory from `mvc.config.json`. By default, **`mvc migrations:run`** and **`mvc migrations:test`** start a **subprocess** of the module’s `index.php` and pass **`--migrations-base=<leaf>`** so the runner knows which directory contains timestamped migration folders.

- **`--force`** — Run even when `migrationsEnabled` is `false` (operators only).

### Create a new migration

Creates a timestamped folder under the leaf `migrations/` directory with `0001_migration.sql` and `0001_migration.rollback.sql`:

```bash
mvc migrations:create --app-path=<app-dir>
```

Override the leaf directory if needed:

```bash
mvc migrations:create --path=<migrations-dir>
```

### Run all pending migrations

```bash
mvc migrations:run --app-path=<app-dir>
```

### Test one migration

Runs apply, rollback, and schema comparison for a single migration name (folder name):

```bash
mvc migrations:test --app-path=<app-dir> --migration=<migration-folder-name>
```

You can also run migrations directly with PHP against the module’s `index.php`. Arguments are passed through to `MigrationApp`; optional **`--migrations-base=<dir>`** overrides the default leaf directory (the same flag the `mvc` CLI forwards).

## Layout

Nested (scaffolded) module:

```
<app>/
  index.php
  mvc.config.json
  <migration-module>/          # e.g. Migrations (from migrationsFolderPath)
    index.php                  # CLI entry for MigrationApp
    MigrationsBootstrap.php    # Env → PDO + MigrationsMysqlConnection + Monolog + Dependencies::configure
    migrations/
      <YYYYMMDDhhmmss>/
        0001_migration.sql
        0001_migration.rollback.sql
```

Flat module (timestamp folders next to `index.php`, no `migrations/` subfolder): ensure `mvc.config.json` points at that module; the CLI resolver uses the module directory as the leaf when `migrations/` is absent but `index.php` exists and timestamp-like subdirectories are present.
