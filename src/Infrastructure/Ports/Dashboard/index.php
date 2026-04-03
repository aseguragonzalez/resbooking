<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\DashboardApp;
use Infrastructure\Ports\Dashboard\DashboardBootstrap;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;

$container = new Container();
DashboardBootstrap::register($container, __DIR__);
$app = new DashboardApp(container: $container, basePath: __DIR__);
$app->addMiddleware(RestaurantContext::class);
$app->useAuthentication();
$app->useAuthorization();
$app->run();
