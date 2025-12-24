<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Application\Restaurants\AddDiningArea\AddDiningArea;
use Application\Restaurants\AddDiningArea\AddDiningAreaService;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurant;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantService;
use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdService;
use Application\Restaurants\RemoveDiningArea\RemoveDiningArea;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaService;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilities;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesService;
use Application\Restaurants\UpdateDiningArea\UpdateDiningArea;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaService;
use Application\Restaurants\UpdateSettings\UpdateSettings;
use Application\Restaurants\UpdateSettings\UpdateSettingsService;
use DI\Container;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Adapters\Notificators\ConsoleChallengeNotificator;
use Infrastructure\Adapters\Repositories\IdentityStore\InFileIdentityStore;
use Infrastructure\Adapters\Repositories\Restaurants\InFileRestaurantRepository;
use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Controllers\AvailabilitiesController;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\DiningAreasController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Infrastructure\Ports\Dashboard\Controllers\RestaurantsController;
use Infrastructure\Ports\Dashboard\Controllers\SettingsController;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\{Logger as MonoLogger, Level};
use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Logging\MonoLoggerAdapter;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;
use Seedwork\Infrastructure\Mvc\WebApp;

final class App extends WebApp
{
    public function __construct(Container $container, DashboardSettings $settings)
    {
        parent::__construct($settings, $container);
    }

    protected function configure(): void
    {
        // configure logger
        $context = [
            "service.name" => $this->settings->serviceName,
            "service.version" => $this->settings->serviceVersion,
            "environment" => $this->settings->environment,
        ];
        $logger = new MonoLogger($context["service.name"]);
        $handler = new StreamHandler('php://stdout', Level::Debug);
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);
        $loggerAdapter = new MonoLoggerAdapter($logger, $context);
        $this->container->set(Logger::class, $loggerAdapter);

        // configure security: IdentityManager, IdentityStore and ChallengeNotificator
        $this->container->set(ChallengeNotificator::class, $this->container->get(ConsoleChallengeNotificator::class));
        $this->container->set(IdentityStore::class, $this->container->get(InFileIdentityStore::class));
        $this->container->set(IdentityManager::class, $this->container->get(DefaultIdentityManager::class));

        // configure application services
        $this->container->set(RestaurantRepository::class, $this->container->get(InFileRestaurantRepository::class));
        $this->container->set(CreateNewRestaurant::class, $this->container->get(CreateNewRestaurantService::class));
        $this->container->set(UpdateSettings::class, $this->container->get(UpdateSettingsService::class));
        $this->container->set(AddDiningArea::class, $this->container->get(AddDiningAreaService::class));
        $this->container->set(RemoveDiningArea::class, $this->container->get(RemoveDiningAreaService::class));
        $this->container->set(UpdateDiningArea::class, $this->container->get(UpdateDiningAreaService::class));
        $this->container->set(UpdateAvailabilities::class, $this->container->get(UpdateAvailabilitiesService::class));
        $this->container->set(GetRestaurantById::class, $this->container->get(GetRestaurantByIdService::class));
    }

    protected function router(): Router
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
