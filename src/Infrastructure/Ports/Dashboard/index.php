<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Container\PhpDiMutableContainer;
use Infrastructure\Ports\Dashboard\DashboardApp;
use Infrastructure\Ports\Dashboard\DashboardBootstrap;
use Infrastructure\Ports\Dashboard\DashboardErrorSettings;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Nyholm\Psr7Server\ServerRequestCreator;

$container = new Container();
$wrapped = new PhpDiMutableContainer($container);
DashboardBootstrap::register($wrapped, __DIR__);

/** @var ServerRequestCreator $requestCreator */
$requestCreator = $wrapped->get(ServerRequestCreator::class);
$request = $requestCreator->fromGlobals();

$app = new DashboardApp(container: $wrapped, basePath: __DIR__);
$app->useErrorSettings(DashboardErrorSettings::create());
$app->addMiddleware(RestaurantContext::class);
$app->useAuthentication();
$app->useRouteAccessControl();

exit($app->run(request: $request));
