<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Seedwork\Infrastructure\Mvc\Settings;
use Infrastructure\Ports\Dashboard\App;

$settings = new Settings(basePath: __DIR__);
$app = new App(new Container(), $settings);

$app->onRequest();
