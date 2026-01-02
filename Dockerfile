FROM php:8.4-apache-bookworm

# Set locale to C.UTF-8
ENV LANG=C.UTF-8 \
    LC_ALL=C.UTF-8

# Install system dependencies and Composer
RUN apt-get update && apt-get install -y --no-install-recommends curl unzip $PHPIZE_DEPS openssl\
    && pecl install xdebug\
    && docker-php-ext-enable xdebug\
    && apt-get purge -y -o APT::AutoRemove::RecommendsImportant=false $PHPIZE_DEPS\
    && apt-get install -y make\
    && apt-get autoremove -y\
    && rm -rf /var/lib/apt/lists/*\
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html
COPY . .
RUN composer install

# Copy Xdebug configuration
COPY deployment/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy Apache configuration files
COPY deployment/apache/ports.conf /etc/apache2/ports.conf
COPY deployment/apache/server-name.conf /etc/apache2/conf-available/server-name.conf

# Create directories for environment variable configs and SSL
RUN mkdir -p /etc/apache2/conf-available/envvars \
    && mkdir -p /etc/apache2/ssl

# Copy virtual host and environment variable configs for each app
# Note: vhost.conf files now contain SSL configurations (mandatory)
COPY deployment/apps/dashboard/vhost.conf /etc/apache2/sites-available/dashboard.conf
COPY deployment/apps/dashboard/envvars.conf /etc/apache2/conf-available/envvars/dashboard.envvars.conf
COPY deployment/apps/coverage/vhost.conf /etc/apache2/sites-available/coverage.conf
COPY deployment/apps/coverage/envvars.conf /etc/apache2/conf-available/envvars/coverage.envvars.conf

# Enable Apache modules and configurations
RUN a2enmod rewrite headers ssl\
    && a2enconf server-name\
    && a2ensite dashboard.conf\
    && a2dissite 000-default.conf 2>/dev/null || true

# Expose ports
EXPOSE 80 443

# Start Apache in foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]
