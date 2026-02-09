<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\BackgroundTasks\BackgroundTasksApp;

$app = new BackgroundTasksApp(
    container: new Container(),
    basePath: __DIR__,
);

$arguments = array_slice($argv ?? [], 1);
exit($app->run(argc: count($arguments), argv: $arguments));
