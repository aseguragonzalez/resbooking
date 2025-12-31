#!/bin/bash
set -e

# Set locale to C.UTF-8 (usually available by default)
export LANG=C.UTF-8
export LC_ALL=C.UTF-8

# Install Composer if not already installed
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    apt-get update && apt-get install -y --no-install-recommends \
        curl \
        unzip \
        && rm -rf /var/lib/apt/lists/*

    # Download and install Composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

    echo "Composer installed successfully"
fi

echo "Setting up Apache Server..."

# Install Xdebug extension if not already installed
if ! php -m | grep -q xdebug; then
    echo "Installing Xdebug extension..."
    # Install dependencies for PECL
    apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        && rm -rf /var/lib/apt/lists/*

    # Install xdebug via PECL
    pecl install xdebug

    echo "Enabling Xdebug..."
    docker-php-ext-enable xdebug || echo "Xdebug already enabled or not available"
fi

# Install PHP extensions if needed
if ! php -m | grep -q pdo_mysql; then
    echo "Installing PDO MySQL extension..."
    docker-php-ext-install pdo_mysql
fi

# Copy Apache ports configuration
echo "Configuring Apache ports..."
cp /var/www/html/deployment/apache/ports.conf /etc/apache2/ports.conf

# Set ServerName to suppress warning
echo "Configuring Apache ServerName..."
cp /var/www/html/deployment/apache/server-name.conf /etc/apache2/conf-available/server-name.conf
a2enconf server-name

# Copy and enable per-webapp environment variables
echo "Configuring Apache environment variables..."
mkdir -p /etc/apache2/conf-available/envvars
for envfile in /var/www/html/deployment/apache/envvars/*.envvars.conf; do
    if [ -f "$envfile" ]; then
        filename=$(basename "$envfile")
        echo "  - Copying $filename..."
        cp "$envfile" "/etc/apache2/conf-available/envvars/$filename"
    fi
done

# Enable required Apache modules
echo "Enabling Apache modules..."
a2enmod rewrite headers

# Enable all available sites
echo "Enabling webapp sites..."
for site in /var/www/html/deployment/apache/sites-available/*.conf; do
    if [ -f "$site" ]; then
        site_name=$(basename "$site" .conf)
        if [ "$site_name" != "README" ]; then
            echo "  - Enabling $site_name..."
            cp "$site" "/etc/apache2/sites-available/$site_name.conf"
            a2ensite "$site_name.conf"
        fi
    fi
done

# Disable default site
a2dissite 000-default.conf 2>/dev/null || true

echo "Apache setup complete!"
