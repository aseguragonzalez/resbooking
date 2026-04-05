<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use Framework\Web\ErrorMapping;
use Framework\Web\ErrorSettings;
use Framework\Web\Routes\AccessDeniedException;
use Framework\Web\Routes\AuthenticationRequiredException;
use Framework\Web\Routes\RouteDoesNotFoundException;

final class DashboardErrorSettings
{
    public static function create(): ErrorSettings
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
}
