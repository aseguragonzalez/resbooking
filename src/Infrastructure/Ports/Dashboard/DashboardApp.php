<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Framework\Mvc\MvcWebApp;
use Framework\Mvc\Routes\Router;
use Infrastructure\Ports\Dashboard\Controllers\RouterBuilder;

final class DashboardApp extends MvcWebApp
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    protected function router(): Router
    {
        return RouterBuilder::build();
    }
}
