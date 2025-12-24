<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Seedwork\Infrastructure\Mvc\ErrorMapping;
use Seedwork\Infrastructure\Mvc\Routes\AccessDeniedException;
use Seedwork\Infrastructure\Mvc\Routes\AuthenticationRequiredException;
use Seedwork\Infrastructure\Mvc\Routes\RouteDoesNotFoundException;
use Seedwork\Infrastructure\Mvc\Settings;

final class DashboardSettings extends Settings
{
    public function __construct(
        string $basePath,
        string $environment = 'local',
        public readonly string $restaurantCookieName = 'restaurant',
        public readonly string $restaurantSelectionUrl = '/restaurants/select',
    ) {
        parent::__construct(
            basePath: $basePath,
            environment: $environment,
            errorsMappingDefaultValue: $this->getDefaultErrorsMapping(),
            errorsMapping: $this->getErrorsMapping(),
            languages: ['en'],
            serviceName: 'dashboard',
            serviceVersion: '1.0.0',
        );
    }

    /**
     * @return array<class-string<\Throwable>, ErrorMapping>
     */
    private function getErrorsMapping(): array
    {
        return [
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
    }

    private function getDefaultErrorsMapping(): ErrorMapping
    {
        return new ErrorMapping(
            statusCode: 500,
            templateName: 'Shared/500',
            pageTitle: '{{internalServerError.title}}'
        );
    }
}
