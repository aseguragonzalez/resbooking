<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;

class DashboardController extends RestaurantBaseController
{
    public function __construct(
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    public function index(): ActionResponse
    {
        return $this->view(model: (object)[
            'pageTitle' => 'Dashboard',
            'model' => (object)[
                'title' => 'Welcome to the Dashboard',
            ],
        ]);
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/'),
                controller: DashboardController::class,
                action: 'index',
                authRequired: true
            )
        ];
    }
}
