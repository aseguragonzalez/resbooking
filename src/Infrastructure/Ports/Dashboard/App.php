<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Infrastructure\Dependencies;
use Infrastructure\Ports\Dashboard\Controllers\RouterBuilder;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Seedwork\Infrastructure\Logging\LoggerSettings;
use Seedwork\Infrastructure\Mvc\AuthSettings;
use Seedwork\Infrastructure\Mvc\ErrorMapping;
use Seedwork\Infrastructure\Mvc\ErrorSettings;
use Seedwork\Infrastructure\Mvc\HtmlViewEngineSettings;
use Seedwork\Infrastructure\Mvc\LanguageSettings;
use Seedwork\Infrastructure\Mvc\Routes\AccessDeniedException;
use Seedwork\Infrastructure\Mvc\Routes\AuthenticationRequiredException;
use Seedwork\Infrastructure\Mvc\Routes\RouteDoesNotFoundException;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\WebApp;

final class App extends WebApp
{
    public function __construct(Container $container, private readonly string $basePath)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        // configure application services
        Dependencies::configure($this->container);

        // configure middlewares
        $this->addMiddleware(RestaurantContext::class);
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

    protected function router(): Router
    {
        return RouterBuilder::build();
    }
}
