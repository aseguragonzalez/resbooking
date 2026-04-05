<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Framework\Mvc\MvcWebApp;
use Framework\Mvc\Requests\RequestContext;
use Psr\Container\ContainerInterface;

final class DashboardApp extends MvcWebApp
{
    public function __construct(ContainerInterface $container, string $basePath, RequestContext $requestContext)
    {
        parent::__construct($container, $basePath, $requestContext);
    }
}
