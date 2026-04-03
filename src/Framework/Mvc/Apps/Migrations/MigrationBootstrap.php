<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations;

use DI\Container;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

final class MigrationBootstrap
{
    public static function registerFromEnvironment(Container $container): void
    {
        $container->set(
            MigrationSettings::class,
            new MigrationSettings(
                host: getenv('MIGRATIONS_DATABASE_HOST') ?: 'mariadb',
                database: getenv('MIGRATIONS_DATABASE_NAME') ?: 'migrations',
                user: getenv('MIGRATIONS_DATABASE_USER') ?: 'migrations',
                password: getenv('MIGRATIONS_DATABASE_PASSWORD') ?: '',
            ),
        );

        $serviceName = getenv('MIGRATIONS_SERVICE_NAME') ?: 'migrations';
        $logLevel = getenv('MIGRATIONS_LOG_LEVEL') ?: 'debug';

        $handler = new StreamHandler(
            stream: 'php://stdout',
            level: self::logLevelFromSettings($logLevel)
        );
        $handler->setFormatter(new LineFormatter(
            format: '[%datetime%] %level_name%: %message%',
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));

        $logger = new Logger($serviceName);
        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $container->set(LoggerInterface::class, $logger);

        Dependencies::configure($container);
    }

    private static function logLevelFromSettings(string $logLevel): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $normalized = strtolower($logLevel);
        if (!in_array($normalized, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }

        return Level::fromName($normalized);
    }
}
