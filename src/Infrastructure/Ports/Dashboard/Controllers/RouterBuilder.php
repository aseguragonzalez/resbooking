<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Controllers\AvailabilitiesController;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\DiningAreasController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Infrastructure\Ports\Dashboard\Controllers\RestaurantsController;
use Infrastructure\Ports\Dashboard\Controllers\SettingsController;
use Seedwork\Infrastructure\Mvc\Routes\Router;

final class RouterBuilder
{
    public static function build(): Router
    {
        $accountsRoutes = AccountsController::getRoutes();
        $reservationsRoutes = ReservationsController::getRoutes();
        $dashboardRoutes = DashboardController::getRoutes();
        $restaurantRoutes = SettingsController::getRoutes();
        $diningAreasRoutes = DiningAreasController::getRoutes();
        $availabilitiesRoutes = AvailabilitiesController::getRoutes();
        $restaurantsRoutes = RestaurantsController::getRoutes();
        return new Router(routes:[
            ...$accountsRoutes,
            ...$reservationsRoutes,
            ...$dashboardRoutes,
            ...$restaurantRoutes,
            ...$diningAreasRoutes,
            ...$availabilitiesRoutes,
            ...$restaurantsRoutes
        ]);
    }
}
