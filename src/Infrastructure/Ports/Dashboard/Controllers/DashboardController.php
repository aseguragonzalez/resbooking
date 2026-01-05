<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

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
