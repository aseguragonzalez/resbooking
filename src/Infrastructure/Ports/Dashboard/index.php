<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\App;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Seedwork\Infrastructure\Mvc\LanguageSettings;

$LanguageSettings = new LanguageSettings(i18nPath: __DIR__ . '/assets/i18n');

$container = new Container();
$container->set(LanguageSettings::class, $LanguageSettings);

$settings = new DashboardSettings(basePath: __DIR__, environment: getenv('ENVIRONMENT') ?: 'local');
$app = new App($container, $settings);

$app->handleRequest();
