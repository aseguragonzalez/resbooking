<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Framework\Logging\LoggerAdapter;
use Framework\Logging\LoggerSettings;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\HtmlViewEngineSettings;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\MvcWebApp;
use Framework\Mvc\Routes\AccessDeniedException;
use Framework\Mvc\Routes\AuthenticationRequiredException;
use Framework\Mvc\Routes\RouteDoesNotFoundException;
use Framework\Mvc\Routes\Router;
use Infrastructure\Dependencies;
use Infrastructure\Ports\Dashboard\Controllers\RouterBuilder;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

final class DashboardApp extends MvcWebApp
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    protected function configureDependencies(): void
    {
        Dependencies::configure($this->container);
    }

    protected function configureLogging(): void
    {
        /** @var LoggerSettings $loggerSettings */
        $loggerSettings = $this->container->get(LoggerSettings::class);

        $handler = new StreamHandler($loggerSettings->stream, $this->getLogLevel($loggerSettings));
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

        $loggerAdapter = new LoggerAdapter(logger: $logger);
        $this->container->set(LoggerInterface::class, $loggerAdapter);
    }

    protected function configureSettings(): void
    {
        $this->container->set(AuthSettings::class, new AuthSettings('/accounts/sign-in', '/accounts/sign-out'));
        $this->container->set(RestaurantContextSettings::class, new RestaurantContextSettings());
        $this->container->set(ErrorSettings::class, $this->getErrorSettings());
        $this->container->set(HtmlViewEngineSettings::class, new HtmlViewEngineSettings(basePath: $this->basePath));
        $this->container->set(LanguageSettings::class, new LanguageSettings(basePath: $this->basePath));

        $loggerSettings = new LoggerSettings(
            environment: getenv('ENVIRONMENT') ?: 'local',
            serviceName: getenv('DASHBOARD_SERVICE_NAME') ?: 'dashboard',
            serviceVersion: getenv('DASHBOARD_SERVICE_VERSION') ?: '1.0.0',
            logLevel: getenv('DASHBOARD_LOG_LEVEL') ?: 'debug',
            stream: getenv('DASHBOARD_LOG_STREAM') ?: 'php://stdout',
        );
        $this->container->set(LoggerSettings::class, $loggerSettings);
    }

    protected function router(): Router
    {
        return RouterBuilder::build();
    }

    private function getErrorSettings(): ErrorSettings
    {
        $errorsMapping = [
            RouteDoesNotFoundException::class => new ErrorMapping(
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

    private function getLogLevel(LoggerSettings $loggerSettings): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $logLevel = strtolower($loggerSettings->logLevel);
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$loggerSettings->logLevel}");
        }
        return Level::fromName($logLevel);
    }
}
