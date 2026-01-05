<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\App;

$app = new App(container: new Container(), basePath: __DIR__);

$app->handleRequest();
