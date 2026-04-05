<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Framework\Mvc\Requests\RequestContext;
use Infrastructure\Container\PhpDiMutableContainer;
use Infrastructure\Ports\Dashboard\DashboardApp;
use Infrastructure\Ports\Dashboard\DashboardBootstrap;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Nyholm\Psr7Server\ServerRequestCreator;

$container = new Container();
DashboardBootstrap::register($container, __DIR__);
$wrapped = new PhpDiMutableContainer($container);
$requestContext = new RequestContext();
$wrapped->set(RequestContext::class, $requestContext);

/** @var ServerRequestCreator $requestCreator */
$requestCreator = $wrapped->get(ServerRequestCreator::class);
$request = $requestCreator->fromGlobals();

$app = new DashboardApp(container: $wrapped, basePath: __DIR__, requestContext: $requestContext);
$app->addMiddleware(RestaurantContext::class);
$app->useAuthentication();
$app->useRouteAccessControl();
$app->run($request);
