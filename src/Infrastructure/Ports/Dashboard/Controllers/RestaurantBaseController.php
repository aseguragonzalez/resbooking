<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Framework\Mvc\Controllers\Controller;
use Framework\Mvc\Requests\RequestContext;

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
