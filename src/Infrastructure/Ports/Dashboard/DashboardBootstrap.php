<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\LoggerSettings;
use Framework\Mvc\Config\MvcConfig;
use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\HtmlViewEngineSettings;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Routes\AccessDeniedException;
use Framework\Mvc\Routes\AuthenticationRequiredException;
use Framework\Mvc\Routes\RouteDoesNotFoundException;
use Framework\Mvc\Security\Domain\Services\ChallengeNotificator;
use Framework\Mvc\UiAssetsSettings;
use Infrastructure\Dependencies;
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
        self::registerSettings($container, $basePath);
        self::registerLogging($container);
        self::registerDependencies($container);
    }

    private static function registerSettings(Container $container, string $basePath): void
    {
        $container->set(AuthSettings::class, new AuthSettings('/accounts/sign-in', '/accounts/sign-out'));
        $container->set(RestaurantContextSettings::class, new RestaurantContextSettings());
        $container->set(ErrorSettings::class, self::errorSettings());
        $container->set(HtmlViewEngineSettings::class, new HtmlViewEngineSettings(basePath: $basePath));
        $container->set(LanguageSettings::class, new LanguageSettings(basePath: $basePath));
        $container->set(UiAssetsSettings::class, UiAssetsSettings::fromConfig(MvcConfig::defaults()));

        $container->set(
            LoggerSettings::class,
            new LoggerSettings(
                environment: getenv('ENVIRONMENT') ?: 'local',
                serviceName: getenv('DASHBOARD_SERVICE_NAME') ?: 'dashboard',
                serviceVersion: getenv('DASHBOARD_SERVICE_VERSION') ?: '1.0.0',
                logLevel: getenv('DASHBOARD_LOG_LEVEL') ?: 'debug',
            )
        );

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
        /** @var LoggerSettings $loggerSettings */
        $loggerSettings = $container->get(LoggerSettings::class);

        $handler = new RotatingFileHandler(
            filename: "/var/log/apache2/dashboard.log",
            maxFiles: 10,
            level: self::logLevel($loggerSettings)
        );
        $handler->setFormatter(new LineFormatter(
            format: '[%datetime%] %level_name%: %message%',
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));

        $logger = new Logger($loggerSettings->serviceName);
        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $container->set(LoggerInterface::class, $logger);
    }

    private static function registerDependencies(Container $container): void
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
    }

    private static function errorSettings(): ErrorSettings
    {
        $errorsMapping = [
            RouteDoesNotFoundException::class => new ErrorMapping(
                statusCode: 404,
                templateName: 'Shared/404',
                pageTitle: '{{notFound.title}}'
            ),
            DiningAreaNotFound::class => new ErrorMapping(
                statusCode: 404,
                templateName: 'Shared/404',
                pageTitle: '{{notFound.title}}'
            ),
            AuthenticationRequiredException::class => new ErrorMapping(
                statusCode: 401,
                templateName: 'Shared/401',
                pageTitle: '{{unauthenticated.title}}'
            ),
            AccessDeniedException::class => new ErrorMapping(
                statusCode: 403,
                templateName: 'Shared/403',
                pageTitle: '{{accessDenied.title}}'
            ),
        ];

        $defaultErrorMapping = new ErrorMapping(
            statusCode: 500,
            templateName: 'Shared/500',
            pageTitle: '{{internalServerError.title}}'
        );

        return new ErrorSettings(
            errorsMapping: $errorsMapping,
            errorsMappingDefaultValue: $defaultErrorMapping
        );
    }

    private static function logLevel(LoggerSettings $loggerSettings): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $logLevel = strtolower($loggerSettings->logLevel);
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$loggerSettings->logLevel}");
        }

        return Level::fromName($logLevel);
    }
}
