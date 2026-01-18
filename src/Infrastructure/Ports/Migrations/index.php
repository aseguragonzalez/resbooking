<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Framework\Migrations\MigrationApp;

$migrationApp = new MigrationApp(
    container: new Container(),
    basePath:  __DIR__ . '/migrations',
);

// remove the script name from the arguments
$arguments = array_slice($argv, 1);

// run the application
$migrationApp->run(argc: count($arguments), argv: $arguments);
