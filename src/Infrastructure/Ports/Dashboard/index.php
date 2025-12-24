<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\App;
use Infrastructure\Ports\Dashboard\DashboardSettings;

$settings = new DashboardSettings(basePath: __DIR__, environment: 'local');
$app = new App(new Container(), $settings);

$app->handleRequest();
