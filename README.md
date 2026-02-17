# Resbooking

A simple, self-hosted restaurant reservation system that helps restaurants
manage online and phone reservations. Originally built as an MVP, this project
has been refactored to use modern PHP best practices and is now open source.

## Why This Project?

Over ten years ago, I built this project as an MVP while working on a startup
idea. It was my first attempt at solving a real-world problem: helping
restaurants manage their reservations efficiently.

Although the startup didn't take off, this project remained important to me.
Looking back, I saw an opportunity to improve it—upgrading it from `PHP 5.5` to
`PHP 8.4`, applying modern best practices, and making it cleaner and easier
to maintain.

Now, I want to give it a second life by sharing it as an open-source project.
Hopefully, others can use, learn from, and contribute to it. Whether you're a
restaurant owner looking for a simple reservation system or a developer
exploring PHP, I hope this project helps you in some way!

### Features

- **Reservation System** – Customers can book tables online.
- **Restaurant Configuration** – Set up tables, capacity, and available slots
per day and shift.
- **Email Notifications** – Send confirmations and reminders to customers.
- ~~**Promotions & Offers** – Restaurants can publish special offers based on
the day and shift.~~

### Tech Stack

- **Backend:** PHP 8.4 (upgraded from PHP 5.5) (MVC architecture over Apache)
- **Database:** MariaDB
- **Frontend:** Javascript, HTML5, CSS3

### Documentation

- **Seedwork (DDD building blocks):** [docs/seedwork/README.md](docs/seedwork/README.md) — goal, usage, and component reference for the in-repo Seedwork library.

## Requirements

- [Docker][docker]
- [VS Code][vscode]
- [Dev Containers Extension][devcontainers]

## Setup

### Setup for Development

First-time setup for contributors. Follow these steps in order.

#### 1. Prerequisites

- Docker, VS Code, and the [Dev Containers Extension][devcontainers] installed
- [mkcert][mkcert] installed on your host machine (for locally-trusted SSL certificates):

  ```bash
  # macOS
  brew install mkcert
  brew install nss  # For Firefox support

  # Linux
  sudo apt install libnss3-tools
  curl -JLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
  chmod +x mkcert-v*-linux-amd64
  sudo mv mkcert-v*-linux-amd64 /usr/local/bin/mkcert

  # Windows
  choco install mkcert
  ```

#### 2. Host configuration

Add the following entries to your local `/etc/hosts` file to map application
domains to `127.0.0.1`:

```text
127.0.0.1    dashboard
127.0.0.1    coverage
```

#### 3. Create .env files

Create a root `.env` file (see [Environment Variables](#environment-variables)
for the full template). You need:

- Root `.env` — application and database credentials (use
  `deployment/apps/dashboard/.env.example` and
  `deployment/apps/background-tasks/.env.example` as references)
- `deployment/database/.env` — MariaDB configuration (copy from
  `deployment/database/.env.example`)

Ensure `.env` files are not committed (they are gitignored). Copy from examples
and update with your values.

#### 4. Generate SSL certificates (on host, before starting the container)

```bash
make setup-ssl-all
```

Or for a specific app: `make setup-ssl APP=dashboard`. Certificates are stored
in `deployment/ssl/`. SSL is mandatory — all sites require certificates to
function.

#### 5. Enable site (inside container)

After the Dev Container has started:

```bash
bash deployment/scripts/enable-site.sh dashboard
```

The script verifies SSL certificates exist before enabling the site.

#### 6. Create database users and update .env

The database is created automatically on first MariaDB startup. Create the dashboard
user:

```bash
make create-user USER=dashboard
```

The script prints a generated password. Update your root `.env` with
`DASHBOARD_DATABASE_USER=dashboard` and
`DASHBOARD_DATABASE_PASSWORD=<generated-password>`. Optionally create a
`background_tasks` user: `make create-user USER=background_tasks` and set
`BACKGROUND_TASKS_DATABASE_*` in `.env` accordingly.

#### 7. Sync/persist .env

Keep your `.env` files out of version control (they are gitignored). If you recreate
the project or switch branches, copy again from the `.env.example` templates
and re-apply your credentials.

#### 8. Execute migrations

```bash
make migrate
```

#### 9. Open in Dev Container and install dependencies

Open the project in VS Code or Cursor (with the Dev Containers extension). Wait
for the Dev Container to build, then run:

```bash
make install
```

**Accessing applications:** Dashboard: `https://dashboard`; Coverage:
`https://coverage` (after running tests with coverage)

**Certificate details:** Certificates are in `deployment/ssl/`, valid 1 year
(mkcert default), trusted by browsers. Each app: dashboard.crt, coverage.crt.

### Setup for Demo

For running a demo instance (showcase, internal testing) without the Dev Container
workflow:

- **Deployment:** `docker compose up -d apache mariadb smtp4dev`
- **SSL:** Real certs (Let's Encrypt) or self-signed in `deployment/ssl/` as
  `dashboard.crt` and `dashboard.key`. For internal demos, use mkcert.
- **Host/DNS:** Resolve dashboard hostname to server IP or add `/etc/hosts`
  entries on client machines.
- **Environment:** Set `ENVIRONMENT=demo` in `.env`. Configure real SMTP for
  actual emails, or keep `smtp4dev` for captured messages.
- **Database:** Create `deployment/database/.env` and root `.env`, create users
  (`make create-user USER=dashboard`), update `.env`, then `make migrate`.
- **Enable site:** `bash deployment/scripts/enable-site.sh dashboard` (inside
  apache container).

## Environment Variables

Configure environment variables during [Setup for Development](#setup-for-development)
or [Setup for Demo](#setup-for-demo). Create a root `.env` and `deployment/database/.env`
from the examples below.

**Root `.env`** (references: `deployment/apps/dashboard/.env.example`,
`deployment/apps/background-tasks/.env.example`):

```env
DASHBOARD_DATABASE_HOST=mariadb
DASHBOARD_DATABASE_NAME=reservations
DASHBOARD_DATABASE_PASSWORD=user_password_here
DASHBOARD_DATABASE_USER=dashboard
DASHBOARD_DEFAULT_HOST=http://dashboard
DASHBOARD_SERVICE_NAME=dashboard
DASHBOARD_SERVICE_VERSION=0.1.0
ENVIRONMENT=local
XDEBUG_MODE=coverage,debug,develop
SMTP_HOST=smtp4dev
SMTP_PORT=25
SMTP_USER=
SMTP_PASSWORD=
SMTP_ENCRYPTION=
MAIL_FROM_ADDRESS=no-reply@example.test
MAIL_FROM_NAME=dashboard-dev
EMAIL_TEMPLATES_PATH=/var/www/html/resources/email
APP_BASE_URL=https://dashboard
```

**`deployment/database/.env`** (MariaDB config; copy from `deployment/database/.env.example`):

```env
MARIADB_ROOT_PASSWORD=root_password_here
MARIADB_DATABASE=reservations
MARIADB_USER=migrations
MARIADB_PASSWORD=user_password_here
```

## SMTP mock server (smtp4dev)

This project uses [smtp4dev](https://github.com/rnwood/smtp4dev) as a local SMTP
server for development and testing. Docker Compose defines an `smtp4dev` service
that captures outgoing email instead of sending it to a real SMTP server.

- **Web UI:** `http://localhost:8080/` — view and inspect captured messages.
- **SMTP:** From other containers (e.g. `apache`), use hostname `smtp4dev` and
  port `25` (no auth required in development).

The `apache` container reads SMTP settings from the `.env` file
(`SMTP_HOST`, `SMTP_PORT`, etc.). With the defaults above, the BackgroundTasks
app and email handlers send challenge emails (sign-up, reset password) to
smtp4dev instead of a real mail server.

To test email notifications end-to-end:

1. Start the stack (from the project root):

   ```bash
   docker compose up apache mariadb smtp4dev
   ```

2. Trigger actions in the Dashboard that generate sign-up or reset-password
   challenges (e.g. user sign-up or password reset).

3. Run the BackgroundTasks app so it processes pending tasks (e.g. from inside
   the container: `make background-tasks`, or `docker compose exec apache make background-tasks`).

4. Open the smtp4dev web UI in your browser:

   ```text
   http://localhost:8080/
   ```

5. Verify that challenge emails appear with the expected recipient, subject,
   and activation/reset links.

## Getting Started

After completing [Setup for Development](#setup-for-development), use the following
commands for day-to-day development. Common tasks are automated using the `Makefile`.

### Make commands

**Install dependencies:**

Install dependencies and pre-commit checks:

```shell
make install
```

**Clean up:**

Remove dependencies and build artifacts:

```shell
make clean
```

**Run tests:**

```shell
make test
```

**Format code:**

Fix formatting issues:

```shell
make format
```

or check formatting issues:

```shell
make format-check
```

**Check for linting issues:**

```shell
make lint
```

**Run static analysis:**

```shell
make static-analyse
```

**Update autoload:**

```shell
make update-autoload
```

### How to manage the Apache server

**Disable site:**

```shell
bash deployment/scripts/disable-site.sh <webapp-name>
```

**Enable site:**

```shell
bash deployment/scripts/enable-site.sh <webapp-name>
```

Note: SSL certificates are required. The script will verify certificates exist
 before enabling the site.

**Reload Apache:**

```shell
apache2ctl graceful
```

**Restart Apache:**

```shell
apache2ctl restart
```

**Check Apache logs:**

Check the access logs for the specific webapp:

```shell
tail -f /var/log/apache2/<webapp-name>-access.log
```

or check the error logs:

```shell
tail -f /var/log/apache2/<webapp-name>-error.log
```

### How to manage the MariaDB service

**Check MariaDB status:**

```shell
docker-compose ps mariadb
```

**View MariaDB logs:**

```shell
docker-compose logs mariadb
```

**Connect to MariaDB:**

```shell
docker-compose exec mariadb mysql -u dashboard -p reservations
```

**Backup database:**

```shell
docker-compose exec mariadb mysqldump -u root -p reservations > backup.sql
```

**Reset database (WARNING: deletes all data):**

```shell
docker-compose down -v
docker-compose up -d mariadb
```

For more information, see [deployment/database/README.md](deployment/database/README.md).

### How to debug Xdebug

1. Set the Xdebug mode (`XDEBUG_MODE`) to `debug,coverage,develop` in the `.env`.
 It is set to `coverage,debug,develop` by default.
2. Install the Xdebug extension for your browser. [Chrome Xdebug][chrome-xdebug]

## Built With

- [Apache][apache]
- [Composer][composer]
- [PHP][php]
- [PHP Code Sniffer][php-cs]
- [PHP Coding Standards Fixer][php-cs-fixer]
- [PHPStan][php-stan]
- [PHPUnit][php-unit]
- [PHPUnit Test Explorer][vscode-phpunit]
- [Pre-commit][pre-commit]
- [Xdebug][xdebug]

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

[apache]: https://httpd.apache.org/
[chrome-xdebug]: https://chromewebstore.google.com/detail/oiofkammbajfehgpleginfomeppgnglk?utm_source=item-share-cb
[composer]: https://getcomposer.org/
[devcontainers]: https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers
[docker]: https://www.docker.com/
[mkcert]: https://github.com/FiloSottile/mkcert
[php]: https://www.php.net/
[php-cs]: https://github.com/PHPCSStandards/PHP_CodeSniffer/
[php-cs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[php-stan]: https://phpstan.org/user-guide/getting-started
[php-unit]: https://phpunit.de/index.html
[pre-commit]: https://pre-commit.com/
[vscode]: https://code.visualstudio.com/
[vscode-phpunit]: https://marketplace.visualstudio.com/items?itemName=recca0120.vscode-phpunit
[xdebug]: https://xdebug.org/
