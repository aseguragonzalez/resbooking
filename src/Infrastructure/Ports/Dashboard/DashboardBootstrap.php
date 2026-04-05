<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Framework\Web\Routes\Router;
use Framework\Web\Dependencies as MvcWebDependencies;
use Framework\Module\Security\Domain\Services\ChallengeNotificator;
use Infrastructure\Adapters\BackgroundTaskChallengeNotificator;
use Infrastructure\Container\PhpDiMutableContainer;
use Infrastructure\Dependencies;
use Infrastructure\Ports\Dashboard\Controllers\RouterBuilder;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use PDO;
use Psr\Log\LoggerInterface;

final class DashboardBootstrap
{
    public static function register(Container $container, string $basePath): void
    {
        self::registerSettings($container);
        self::registerLogging($container);
        self::registerDependencies($container, $basePath);
    }

    private static function registerSettings(Container $container): void
    {
        $container->set(RestaurantContextSettings::class, new RestaurantContextSettings());

        $container->set(
            DashboardSettings::class,
            new DashboardSettings(
                host: getenv('DASHBOARD_DATABASE_HOST') ?: 'localhost',
                database: getenv('DASHBOARD_DATABASE_NAME') ?: 'dashboard',
                user: getenv('DASHBOARD_DATABASE_USER') ?: 'root',
                password: getenv('DASHBOARD_DATABASE_PASSWORD') ?: '',
            )
        );
    }

    private static function registerLogging(Container $container): void
    {
        $serviceName = getenv('DASHBOARD_SERVICE_NAME') ?: 'dashboard';
        $logLevel = getenv('DASHBOARD_LOG_LEVEL') ?: 'debug';

        $handler = new RotatingFileHandler(
            filename: "/var/log/apache2/dashboard.log",
            maxFiles: 10,
            level: self::logLevel($logLevel)
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
    }

    private static function registerDependencies(Container $container, string $basePath): void
    {
        /** @var DashboardSettings $settings */
        $settings = $container->get(DashboardSettings::class);
        $connection = new PDO(
            $settings->getDsn(),
            $settings->user,
            $settings->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $container->set(PDO::class, $connection);

        Dependencies::configure($container);

        $container->set(ChallengeNotificator::class, $container->get(BackgroundTaskChallengeNotificator::class));

        $container->set(Router::class, RouterBuilder::build());
        MvcWebDependencies::configure(new PhpDiMutableContainer($container), $basePath);
    }

    private static function logLevel(string $logLevel): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $normalized = strtolower($logLevel);
        if (!in_array($normalized, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }

        return Level::fromName($normalized);
    }
}
