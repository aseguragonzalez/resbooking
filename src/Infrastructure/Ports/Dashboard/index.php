<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\App;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Seedwork\Infrastructure\Mvc\AuthSettings;
use Seedwork\Infrastructure\Mvc\LanguageSettings;

$container = new Container();
$container->set(
    AuthSettings::class,
    new AuthSettings(signInPath: '/accounts/sign-in', signOutPath: '/accounts/sign-out')
);
$container->set(LanguageSettings::class, new LanguageSettings(i18nPath: __DIR__ . '/assets/i18n'));

$settings = new DashboardSettings(basePath: __DIR__, environment: getenv('ENVIRONMENT') ?: 'local');
$app = new App($container, $settings);

$app->handleRequest();
