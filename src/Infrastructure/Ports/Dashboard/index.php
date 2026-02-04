<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\DashboardApp;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;

$app = new DashboardApp(container: new Container(), basePath: __DIR__);
$app->addMiddleware(RestaurantContext::class);
$app->useAuthentication();
$app->useAuthorization();
$app->useTransaction();
$app->run();
