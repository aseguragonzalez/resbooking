<?php

declare(strict_types=1);

require_once '/workspaces/resbooking/vendor/autoload.php';

// namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Views\HtmlViewEngine;

$actionParameterBuilder = new ActionParameterBuilder();
$container = new Container();
$responseFactory = new Psr17Factory();
$router = new Router(routes:[
    Route::create(RouteMethod::Get, Path::create('/'), DashboardController::class, 'index'),
    Route::create(RouteMethod::Get, Path::create('/reservations'), ReservationsController::class, 'index'),
    Route::create(RouteMethod::Get, Path::create('/reservations/{id}'), ReservationsController::class, 'edit'),
    Route::create(RouteMethod::Post, Path::create('/reservations/{id}'), ReservationsController::class, 'update')
]);
$viewEngine = new HtmlViewEngine(basePath: __DIR__ . '/Views');
$requestHandler = new RequestHandler(
    $actionParameterBuilder,
    $container,
    $responseFactory,
    $router,
    $viewEngine
);

$method = isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])
    ? $_SERVER['REQUEST_METHOD']
    : 'GET';
$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
    ? $_SERVER['REQUEST_URI']
    : '/';
$requestFactory = new Psr17Factory();
$request = $requestFactory->createServerRequest($method, $uri, $_SERVER);
$request = $request
    ->withQueryParams($_GET)
    ->withParsedBody($_POST)
    ->withCookieParams($_COOKIE)
    ->withUploadedFiles([]);

$response = $requestHandler->handle($request);

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}
echo $response->getBody();
