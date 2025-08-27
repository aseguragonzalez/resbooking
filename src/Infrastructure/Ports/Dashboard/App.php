<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Infrastructure\Ports\Dashboard\Controllers\{DashboardController, ReservationsController};
use Monolog\{Logger as MonoLogger, Level};
use Monolog\Handler\StreamHandler;
use Seedwork\Application\Logging\Logger;
use Monolog\Formatter\JsonFormatter;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\WebApp;
use Seedwork\Infrastructure\Mvc\Routes\{Path, Route, RouteMethod, Router};
use Seedwork\Infrastructure\Logging\MonologLogger;

final class App extends WebApp
{
    public function __construct(Container $container, Settings $settings)
    {
        parent::__construct($settings, $container);
    }

    protected function configure(): void
    {
        // read context data from the environment
        $context = [
            "service.name" => $this->settings->serviceName,
            "service.version" => $this->settings->serviceVersion,
            "environment" => $this->settings->environment,
        ];

        // configure logger
        $logger = new MonoLogger($context["service.name"]);
        $handler = new StreamHandler('php://stdout', Level::Debug);
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);
        $this->container->set(Logger::class, new MonologLogger($logger, $context));
    }

    protected function router(): Router
    {
        return new Router(routes:[
            Route::create(RouteMethod::Get, Path::create('/'), DashboardController::class, 'index'),
            Route::create(RouteMethod::Get, Path::create('/reservations'), ReservationsController::class, 'index'),
            Route::create(
                RouteMethod::Get,
                Path::create('/reservations/create'),
                ReservationsController::class,
                'create'
            ),
            Route::create(RouteMethod::Get, Path::create('/reservations/{id}'), ReservationsController::class, 'edit'),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}'),
                ReservationsController::class,
                'update'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}/status'),
                ReservationsController::class,
                'updateStatus'
            )
        ]);
    }
}
