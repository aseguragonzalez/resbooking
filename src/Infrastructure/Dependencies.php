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
use Framework\Logging\MonoLoggerBuilder;
use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\IdentityManager;
use Framework\Mvc\Security\IdentityStore;
use Infrastructure\Adapters\Notificators\ConsoleChallengeNotificator;
use Infrastructure\Adapters\Repositories\IdentityStore\InFileIdentityStore;
use Infrastructure\Adapters\Repositories\Restaurants\InFileRestaurantRepository;
use Psr\Log\LoggerInterface;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        // TODO: move to WebApp and App configuration
        // configure app logger
        /** @var MonoLoggerBuilder $loggerBuilder */
        $loggerBuilder = $container->get(MonoLoggerBuilder::class);
        $container->set(LoggerInterface::class, $loggerBuilder->build());

        // configure application services
        $container->set(RestaurantRepository::class, $container->get(InFileRestaurantRepository::class));
        $container->set(CreateNewRestaurant::class, $container->get(CreateNewRestaurantHandler::class));
        $container->set(UpdateSettings::class, $container->get(UpdateSettingsHandler::class));
        $container->set(AddDiningArea::class, $container->get(AddDiningAreaHandler::class));
        $container->set(RemoveDiningArea::class, $container->get(RemoveDiningAreaHandler::class));
        $container->set(UpdateDiningArea::class, $container->get(UpdateDiningAreaHandler::class));
        $container->set(UpdateAvailabilities::class, $container->get(UpdateAvailabilitiesHandler::class));
        $container->set(GetRestaurantById::class, $container->get(GetRestaurantByIdHandler::class));

        // configure security: IdentityManager, IdentityStore and ChallengeNotificator
        $container->set(ChallengeNotificator::class, $container->get(ConsoleChallengeNotificator::class));
        $container->set(IdentityStore::class, $container->get(InFileIdentityStore::class));
        $container->set(IdentityManager::class, $container->get(DefaultIdentityManager::class));
    }
}
