<?php

declare(strict_types=1);

require_once '/workspaces/resbooking/vendor/autoload.php';

use DI\Container;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\WebApp;

$container = new Container();
$settings = new Settings(
    basePath: __DIR__,
    i18nPath: __DIR__ . '/assets/i18n',
    viewPath: __DIR__ . '/Views',
);

$app = new WebApp($container, $settings);
$app->onRequest();
