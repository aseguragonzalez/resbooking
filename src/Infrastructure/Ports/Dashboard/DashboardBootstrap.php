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
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Framework\Container\MutableContainer;
use Framework\Web\Requests\RequestContext;

final class DashboardBootstrap
{
    public static function register(MutableContainer $container, string $basePath): void
    {
        self::registerLogging($container);
        self::registerDependencies($container, $basePath);
    }

    private static function registerLogging(MutableContainer $container): void
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

    private static function registerDependencies(MutableContainer $container, string $basePath): void
    {
        $wrapped->set(RequestContext::class, new RequestContext());
        $container->set(RestaurantContextSettings::class, new RestaurantContextSettings());
        $settings = new DashboardSettings(
            host: getenv('DASHBOARD_DATABASE_HOST') ?: 'localhost',
            database: getenv('DASHBOARD_DATABASE_NAME') ?: 'dashboard',
            user: getenv('DASHBOARD_DATABASE_USER') ?: 'root',
            password: getenv('DASHBOARD_DATABASE_PASSWORD') ?: '',
        );
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

        $psr17Factory = new Psr17Factory();
        $container->set(Psr17Factory::class, $psr17Factory);
        $container->set(ResponseFactoryInterface::class, $psr17Factory);
        $container->set(ServerRequestCreator::class, new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
        ));

        MvcWebDependencies::configure($container, $basePath);
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
