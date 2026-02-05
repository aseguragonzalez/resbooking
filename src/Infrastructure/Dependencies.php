<?php

declare(strict_types=1);

namespace Infrastructure;

use Application\Restaurants\AddDiningArea\AddDiningArea;
use Application\Restaurants\AddDiningArea\AddDiningAreaHandler;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurant;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantHandler;
use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Application\Restaurants\RemoveDiningArea\RemoveDiningArea;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaHandler;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilities;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesHandler;
use Application\Restaurants\UpdateDiningArea\UpdateDiningArea;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaHandler;
use Application\Restaurants\UpdateSettings\UpdateSettings;
use Application\Restaurants\UpdateSettings\UpdateSettingsHandler;
use DI\Container;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Framework\Mvc\Security\Dependencies as SecurityDependencies;
use Infrastructure\Adapters\Repositories\Restaurants\SqlRestaurantRepository;
use Seedwork\Application\Messaging\DeferredDomainEventsBus;
use Seedwork\Application\Messaging\DomainEventsBus;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        $container->set(DomainEventsBus::class, $container->get(DeferredDomainEventsBus::class));

        // configure application services
        $container->set(RestaurantRepository::class, $container->get(SqlRestaurantRepository::class));
        $container->set(CreateNewRestaurant::class, $container->get(CreateNewRestaurantHandler::class));
        $container->set(UpdateSettings::class, $container->get(UpdateSettingsHandler::class));
        $container->set(AddDiningArea::class, $container->get(AddDiningAreaHandler::class));
        $container->set(RemoveDiningArea::class, $container->get(RemoveDiningAreaHandler::class));
        $container->set(UpdateDiningArea::class, $container->get(UpdateDiningAreaHandler::class));
        $container->set(UpdateAvailabilities::class, $container->get(UpdateAvailabilitiesHandler::class));
        $container->set(GetRestaurantById::class, $container->get(GetRestaurantByIdHandler::class));
        // configure security: IdentityManager, repositories, use cases and ChallengeNotificator
        SecurityDependencies::configure($container);
    }
}
