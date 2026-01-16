<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Infrastructure\Dependencies;
use Infrastructure\Ports\Dashboard\Controllers\RouterBuilder;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Framework\Logging\LoggerSettings;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\HtmlViewEngineSettings;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Routes\AccessDeniedException;
use Framework\Mvc\Routes\AuthenticationRequiredException;
use Framework\Mvc\Routes\RouteDoesNotFoundException;
use Framework\Mvc\Routes\Router;
use Framework\Mvc\WebApp;

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
        $this->useAuthentication();
        $this->useAuthorization();
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
