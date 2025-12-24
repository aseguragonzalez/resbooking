<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\DashboardSettings;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

abstract class RestaurantBaseController extends Controller
{
    protected function __construct(
        private readonly RequestContext $requestContext,
        private readonly DashboardSettings $settings,
    ) {
    }

    protected function getRestaurantId(): string
    {
        return $this->requestContext->get($this->settings->restaurantIdContextKey);
    }
}
