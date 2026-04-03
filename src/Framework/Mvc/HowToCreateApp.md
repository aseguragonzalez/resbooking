# How to Create a New MVC App (CLI)

The MVC framework includes a small scaffolding tool to create a new app structure.

## Create the app scaffold

Run:

```bash
mvc create-app <path> --name=<AppName> --namespace=<Namespace>
```

Example:

```bash
mvc create-app ./src/Ports/MyApp --name=MyApp --namespace=App\\Ports\\MyApp
```

## What gets generated

When you run `mvc create-app`, the CLI will:

1. Create the MVC app folder structure under `<path>`.
2. Generate `mvc.config.json` in the app root.
3. Create i18n file `assets/i18n/en.json`.
4. Create asset directories:
   - `assets/scripts`
   - `assets/styles`

## `mvc.config.json`

The scaffolded config file stores the locations and bundle filenames the framework templates and features will use, including:

- JavaScript assets path (default: `./assets/scripts`)
- Main JavaScript bundler name (default: `main.min.js`)
- CSS assets path (default: `./assets/styles`)
- Main CSS bundler name (default: `main.min.css`)
- i18n base path (default: `./assets/i18n`)
- `migrationsFolderPath` (empty by default)
- `migrationsEnabled` (`false` by default; set `true` when you run `mvc migrations:enable`)
- `backgroundTasksFolderPath` (empty by default)
- `authenticationEnabled` (`false` by default; set `true` when you run `mvc auth:enable`)

For migrations workflow and CLI commands, see [How to use MVC database migrations](./HowToMigrations.md).

For authentication CLI and middleware wiring, see [How to enable MVC authentication and authorization (CLI)](./HowToAuthentication.md).
