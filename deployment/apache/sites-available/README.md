# Apache Site Configuration Templates

Each webapp should have a corresponding Apache virtual host configuration file here.

## Existing Sites

- `dashboard.conf` - Main Dashboard application (PHP, port 8080)
- `coverage.conf` - PHPUnit coverage reports (Static HTML, port 9000)

## Naming Convention

- File name: `<webapp-name>.conf`
- ServerName: `<webapp-name>.localhost` (for development)
- DocumentRoot: Path to webapp's document root

## Template for PHP Applications

Use this template for PHP webapps (like Dashboard):

```apache
<VirtualHost *:8080>
    ServerName <webapp-name>.localhost
    DocumentRoot /var/www/html/src/Infrastructure/Ports/<WebappName>

    <Directory /var/www/html/src/Infrastructure/Ports/<WebappName>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/<webapp-name>-error.log
    CustomLog ${APACHE_LOG_DIR}/<webapp-name>-access.log combined
    LogLevel warn

    <FilesMatch \.php$>
        SetHandler application/x-httpd-php
    </FilesMatch>
</VirtualHost>
```

## Template for Static Sites

Use this template for static file serving (like Coverage):

```apache
<VirtualHost *:9000>
    ServerName <webapp-name>.localhost
    DocumentRoot /var/www/html/<path-to-static-files>

    <Directory /var/www/html/<path-to-static-files>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/<webapp-name>-error.log
    CustomLog ${APACHE_LOG_DIR}/<webapp-name>-access.log combined
    LogLevel warn
</VirtualHost>
```

## Port Selection

- **8080**: Main webapps (Dashboard, future public-facing apps)
- **9000**: Development tools (Coverage, future dev tools)
- Add new ports to `deployment/apache/ports.conf` if needed
