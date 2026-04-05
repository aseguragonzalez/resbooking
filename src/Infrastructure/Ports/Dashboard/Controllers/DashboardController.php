<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Framework\Web\Actions\MvcAction;
use Framework\Web\Actions\Responses\ActionResponse;
use Framework\Web\Requests\RequestContext;
use Framework\Web\Routes\Path;
use Framework\Web\Routes\Route;
use Framework\Web\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;

final class DashboardController extends RestaurantBaseController
{
    public function __construct(
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    #[MvcAction]
    public function index(): ActionResponse
    {
        return $this->view('Dashboard/index', model: (object)[
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
