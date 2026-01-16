<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Framework\Logging\LoggerSettings;
use Framework\Migrations\Infrastructure\MigrationSettings;
use Framework\Migrations\MigrationApp;

$container = new Container();

$loggerSettings = new LoggerSettings(
    environment: getenv('ENVIRONMENT') ?: 'local',
    serviceName: getenv('MIGRATIONS_SERVICE_NAME') ?: 'migrations',
    serviceVersion: getenv('MIGRATIONS_SERVICE_VERSION') ?: '1.0.0',
    logLevel: getenv('MIGRATIONS_LOG_LEVEL') ?: 'debug',
    stream: getenv('MIGRATIONS_LOG_STREAM') ?: 'php://stdout',
);
$container->set(LoggerSettings::class, $loggerSettings);

$migrationSettings = new MigrationSettings(
    host: getenv('MIGRATIONS_DATABASE_HOST') ?: 'mariadb',
    database: getenv('MIGRATIONS_DATABASE_NAME') ?: 'migrations',
    user: getenv('MIGRATIONS_DATABASE_USER') ?: 'migrations',
    password: getenv('MIGRATIONS_DATABASE_PASSWORD') ?: '',
);
$container->set(MigrationSettings::class, $migrationSettings);

$migrationApp = new MigrationApp(
    container: $container,
    basePath:  __DIR__ . '/migrations',
);

// Parse command line arguments
$options = getopt('', ['test:']);
/** @var string $command */
$command = $options['command'] ?? 'run';
/** @var string|null $testMigrationName */
$testMigrationName = $options['test'] ?? null;
$migrationApp->run($command, $testMigrationName);
