<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Framework\Mvc\Routes\Router;
use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Controllers\AvailabilitiesController;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\DiningAreasController;
use Infrastructure\Ports\Dashboard\Controllers\RestaurantsController;
use Infrastructure\Ports\Dashboard\Controllers\SettingsController;

final class RouterBuilder
{
    public static function build(): Router
    {
        $accountsRoutes = AccountsController::getRoutes();
        $dashboardRoutes = DashboardController::getRoutes();
        $restaurantRoutes = SettingsController::getRoutes();
        $diningAreasRoutes = DiningAreasController::getRoutes();
        $availabilitiesRoutes = AvailabilitiesController::getRoutes();
        $restaurantsRoutes = RestaurantsController::getRoutes();
        return new Router(routes:[
            ...$accountsRoutes,
            ...$dashboardRoutes,
            ...$restaurantRoutes,
            ...$diningAreasRoutes,
            ...$availabilitiesRoutes,
            ...$restaurantsRoutes
        ]);
    }
}
