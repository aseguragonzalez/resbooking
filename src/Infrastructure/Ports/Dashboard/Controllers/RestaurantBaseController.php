<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

abstract class RestaurantBaseController extends Controller
{
    protected function __construct(
        private readonly RequestContext $requestContext,
        private readonly RestaurantContextSettings $settings,
    ) {
    }

    protected function getRestaurantId(): string
    {
        return $this->requestContext->get($this->settings->contextKey);
    }
}
