<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Framework\Mvc\BackgroundTasks\BaseBackgroundTasksApp;
use Infrastructure\Ports\BackgroundTasks\BackgroundTasksBootstrap;

$container = new Container();
BackgroundTasksBootstrap::register($container, __DIR__);
$app = new BaseBackgroundTasksApp(
    container: $container,
    basePath: __DIR__,
);

$arguments = array_slice($argv ?? [], 1);
exit($app->run(argc: count($arguments), argv: $arguments));
