<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Framework\Mvc\Migrations\MigrationApp;
use Infrastructure\Container\PhpDiMutableContainer;
use Infrastructure\Adapters\Migrations\MigrationsBootstrap;

$container = new Container();
MigrationsBootstrap::register($container);
$app = new MigrationApp(
    container: new PhpDiMutableContainer($container),
    basePath: __DIR__,
);

$arguments = array_slice($argv ?? [], 1);
exit($app->run(argc: count($arguments), argv: $arguments));
