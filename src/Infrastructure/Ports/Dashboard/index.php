<?php

declare(strict_types=1);

require_once '/workspaces/resbooking/vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Views\{BranchesReplacer, I18nReplacer, ModelReplacer, HtmlViewEngine};


// Create a PSR-7 request from the global variables
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
);
$request = $creator->fromGlobals();

// Create the DI container and register services
$container = new Container();
$requestContext = new RequestContext();
$requestContext->set(RequestContextKeys::LANGUAGE->value, 'en-en');
$actionParameterBuilder = new ActionParameterBuilder();
$responseFactory = new Psr17Factory();

$router = new Router(routes:[
    Route::create(RouteMethod::Get, Path::create('/'), DashboardController::class, 'index'),
    Route::create(RouteMethod::Get, Path::create('/reservations'), ReservationsController::class, 'index'),
    Route::create(RouteMethod::Get, Path::create('/reservations/create'), ReservationsController::class, 'create'),
    Route::create(RouteMethod::Get, Path::create('/reservations/{id}'), ReservationsController::class, 'edit'),
    Route::create(RouteMethod::Post, Path::create('/reservations/{id}'), ReservationsController::class, 'update'),
    Route::create(
        RouteMethod::Post,
        Path::create('/reservations/{id}/status'),
        ReservationsController::class,
        'updateStatus'
    )
]);

$branchesReplacer = new BranchesReplacer();
$branchesReplacer->setNext(new ModelReplacer());
$i18nReplacer = new I18nReplacer(requestContext: $requestContext, basePath: __DIR__ . '/assets/i18n');
$i18nReplacer->setNext($branchesReplacer);
$viewEngine = new HtmlViewEngine(basePath: __DIR__ . '/Views', contentReplacer: $i18nReplacer);

$requestHandler = new RequestHandler(
    $actionParameterBuilder,
    $container,
    $responseFactory,
    $router,
    $viewEngine
);

// Handle the request and generate a response
$response = $requestHandler->handle($request);

// Send the response to the client
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}
echo $response->getBody();
