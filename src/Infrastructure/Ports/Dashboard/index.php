<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use DI\Container;
use Infrastructure\Ports\Dashboard\App;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Seedwork\Infrastructure\Logging\LoggerSettings;
use Seedwork\Infrastructure\Mvc\AuthSettings;
use Seedwork\Infrastructure\Mvc\HtmlViewEngineSettings;
use Seedwork\Infrastructure\Mvc\LanguageSettings;

$container = new Container();
$container->set(DashboardSettings::class, new DashboardSettings());
$container->set(
    AuthSettings::class,
    new AuthSettings(
        signInPath: '/accounts/sign-in',
        signOutPath: '/accounts/sign-out'
    )
);
$container->set(
    LanguageSettings::class,
    new LanguageSettings(
        i18nPath: __DIR__ . '/assets/i18n'
    )
);
$container->set(HtmlViewEngineSettings::class, new HtmlViewEngineSettings(basePath: __DIR__));

$loggerSettings = new LoggerSettings(
    environment: getenv('ENVIRONMENT') ?: 'local',
    serviceName: getenv('DASHBOARD_SERVICE_NAME') ?: 'dashboard',
    serviceVersion: getenv('DASHBOARD_SERVICE_VERSION') ?: '1.0.0',
    logLevel: getenv('DASHBOARD_LOG_LEVEL') ?: 'debug',
    stream: getenv('DASHBOARD_LOG_STREAM') ?: 'php://stdout',
);
$container->set(LoggerSettings::class, $loggerSettings);

$app = new App($container);

$app->handleRequest();
