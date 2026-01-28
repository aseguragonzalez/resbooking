<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Framework\Mvc\Controllers\Controller;
use Framework\Mvc\Requests\RequestContext;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;

abstract class RestaurantBaseController extends Controller
{
    protected function __construct(
        private readonly RequestContext $requestContext,
        private readonly RestaurantContextSettings $settings,
    ) {
        parent::__construct();
    }

    protected function getRestaurantId(): string
    {
        return $this->requestContext->get($this->settings->contextKey);
    }
}
