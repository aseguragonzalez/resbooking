# resbooking/seedwork

DDD and Hexagonal Architecture building blocks (aggregates, entities, value objects, command/query handlers).

## Requirements

- PHP 8.4 or later

## Installation

From [Packagist](https://packagist.org) (when published):

```bash
composer require resbooking/seedwork
```

From the same repository (monorepo):

Add to your root `composer.json`:

```json
{
    "repositories": [
        { "type": "path", "url": "./packages/seedwork" }
    ],
    "require": {
        "resbooking/seedwork": "@dev"
    }
}
```

Then run `composer update resbooking/seedwork`.

## Documentation

See the [docs](docs/) folder or the main repository [docs/seedwork](https://github.com/vscode/workspace/tree/main/docs/seedwork) for architecture and usage.

## Development

From the package directory:

```bash
make install
make all
```

- `make test` — run PHPUnit
- `make format` — fix code style with PHP-CS-Fixer
- `make format-check` — check style without changing files
- `make lint` — run PHP_CodeSniffer (PSR-12)
- `make static-analyse` — run PHPStan
- `make clean` — remove vendor, coverage, caches
- `make create-package` — build a zip archive in `dist/`

## Releasing

1. Edit `VERSION` in this directory with the new semantic version (e.g. `0.0.2`, `0.2.0-alpha`).
2. Commit and push to `main`, or merge a pull request.
3. The release workflow runs automatically. If the tag `seedwork-v{VERSION}` does not exist, it validates, builds, and publishes a GitHub Release with the zip artifact.
4. No manual `git tag` or `git push --tags` is required.
