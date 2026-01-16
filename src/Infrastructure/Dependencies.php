<?php

declare(strict_types=1);

namespace Infrastructure;

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
use Framework\Logging\Logger;
use Framework\Logging\MonoLoggerAdapter;
use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\IdentityManager;
use Framework\Mvc\Security\IdentityStore;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        // configure app logger
        $container->set(Logger::class, $container->get(MonoLoggerAdapter::class));

        // configure application services
        $container->set(RestaurantRepository::class, $container->get(InFileRestaurantRepository::class));
        $container->set(CreateNewRestaurant::class, $container->get(CreateNewRestaurantService::class));
        $container->set(UpdateSettings::class, $container->get(UpdateSettingsService::class));
        $container->set(AddDiningArea::class, $container->get(AddDiningAreaService::class));
        $container->set(RemoveDiningArea::class, $container->get(RemoveDiningAreaService::class));
        $container->set(UpdateDiningArea::class, $container->get(UpdateDiningAreaService::class));
        $container->set(UpdateAvailabilities::class, $container->get(UpdateAvailabilitiesService::class));
        $container->set(GetRestaurantById::class, $container->get(GetRestaurantByIdService::class));

        // configure security: IdentityManager, IdentityStore and ChallengeNotificator
        $container->set(ChallengeNotificator::class, $container->get(ConsoleChallengeNotificator::class));
        $container->set(IdentityStore::class, $container->get(InFileIdentityStore::class));
        $container->set(IdentityManager::class, $container->get(DefaultIdentityManager::class));
    }
}
