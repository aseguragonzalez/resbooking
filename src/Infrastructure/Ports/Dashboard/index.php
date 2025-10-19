<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Seedwork\Infrastructure\Mvc\Settings;
use Infrastructure\Ports\Dashboard\App;
use Seedwork\Infrastructure\Mvc\ErrorMapping;
use Seedwork\Infrastructure\Mvc\Routes\AccessDeniedException;
use Seedwork\Infrastructure\Mvc\Routes\AuthenticationRequiredException;
use Seedwork\Infrastructure\Mvc\Routes\RouteDoesNotFoundException;

$errors = [
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

$settings = new Settings(basePath: __DIR__, errorsMapping: $errors, errorsMappingDefaultValue: $defaultErrorMapping);
$app = new App(new Container(), $settings);

$app->onRequest();
