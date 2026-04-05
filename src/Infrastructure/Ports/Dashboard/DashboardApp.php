<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Framework\Web\MvcWebApp;
use Psr\Container\ContainerInterface;

final class DashboardApp extends MvcWebApp
{
    public function __construct(ContainerInterface $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }
}
