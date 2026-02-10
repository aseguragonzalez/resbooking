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

## Requirements

- [Docker][docker]
- [VS Code][vscode]
- [Dev Containers Extension][devcontainers]

## Environment Variables

Before starting, you need to create a `.env` file with the environment variables
 for each application. You can use the `.env.example` file as a template.

```env
DASHBOARD_DATABASE_HOST=mariadb
DASHBOARD_DATABASE_NAME=dashboard
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

Also, you need to create a custom `.env` file for database credentials in the
 `deployment/database` directory. You can use the `.env.example` file as a template.

```env
# MariaDB Configuration
MARIADB_ROOT_PASSWORD=root_password_here
MARIADB_DATABASE=dashboard
MARIADB_USER=dashboard
MARIADB_PASSWORD=user_password_here
```

## Host Configuration

Before accessing the dashboard and coverage reports, you need to configure your
 local `/etc/hosts` file to map the application domains to `127.0.0.1`. Add the
 following entries to your `/etc/hosts` file:

```text
127.0.0.1    dashboard
127.0.0.1    coverage
```

After adding these entries, you can access:

- Dashboard: `https://dashboard` (SSL is mandatory)
- Coverage reports: `https://coverage` (SSL is mandatory, after running tests
  with coverage)

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

## SSL Certificate Setup (Required)

This project requires SSL for all webapps. We use [mkcert][mkcert] to generate
locally-trusted SSL certificates for development. These certificates are
automatically trusted by your browser, so you won't see security warnings.

**SSL is mandatory** - all sites require SSL certificates to function.

### Initial Setup

1. **Install mkcert** on your host machine:

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

2. **Generate certificates**:

   ```bash
   # Generate certificates for all apps
   make setup-ssl-all

   # Or generate for a specific app
   make setup-ssl APP=dashboard
   ```

   This will:
   - Install mkcert's local CA (if not already installed)
   - Generate browser-valid certificates for each app based on their ServerName/ServerAlias
   - Store them in `deployment/ssl/`

3. **Start your devcontainer** - certificates are automatically mounted

4. **Enable the app** (inside container):

   ```bash
   bash deployment/scripts/enable-site.sh dashboard
   ```

   Note: The script will verify SSL certificates exist before enabling the site.

### Accessing Applications

After setup, you can access:

- **Dashboard**: `https://dashboard` (port 443)
- **Coverage**: `https://coverage` (port 443)

HTTP requests are automatically redirected to HTTPS (production-like behavior).

### Certificate Details

- Certificates are stored in `deployment/ssl/`
- They are valid for 1 year (mkcert default)
- Automatically trusted by all browsers
- Work exactly like production certificates
- Each app has its own certificate (dashboard.crt, coverage.crt, etc.)

## Getting Started

Open the project in VS Code or Cursor (with the Dev Containers extension) and
 wait for the Dev Container to build. Common tasks are automated using `Makefile`.

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
docker-compose exec mariadb mysql -u dashboard -p dashboard
```

**Backup database:**

```shell
docker-compose exec mariadb mysqldump -u root -p dashboard > backup.sql
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
