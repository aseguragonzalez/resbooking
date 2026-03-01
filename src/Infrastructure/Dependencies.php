<?php

declare(strict_types=1);

namespace Infrastructure;

use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\AddDiningArea\AddDiningAreaHandler;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantCommand;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantHandler;
use Application\Restaurants\GetDiningAreaById\GetDiningAreaByIdHandler;
use Application\Restaurants\GetDiningAreaById\GetDiningAreaByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaHandler;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesHandler;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaHandler;
use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Application\Restaurants\UpdateSettings\UpdateSettingsHandler;
use DI\Container;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Framework\BackgroundTasks\Dependencies as BackgroundTasksDependencies;
use Framework\Mvc\Security\Dependencies as SecurityDependencies;
use Framework\Mvc\Security\Domain\Services\ChallengeNotificator;
use Framework\Mvc\Security\Infrastructure\BackgroundTaskChallengeNotificator;
use Infrastructure\Adapters\PdoUnitOfWork;
use Infrastructure\Adapters\Repositories\Restaurants\SqlRestaurantRepository;
use PDO;
use SeedWork\Application\CommandBus;
use SeedWork\Application\DomainEventBus;
use SeedWork\Application\QueryBus;
use SeedWork\Infrastructure\ContainerCommandBus;
use SeedWork\Infrastructure\ContainerQueryBus;
use SeedWork\Infrastructure\DeferredDomainEventBus;
use SeedWork\Infrastructure\DomainEventFlushCommandBus;
use SeedWork\Infrastructure\TransactionalCommandBus;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        $container->set(DomainEventBus::class, $container->get(DeferredDomainEventBus::class));

        $container->set(RestaurantRepository::class, $container->get(SqlRestaurantRepository::class));
        $containerCommandBus = new ContainerCommandBus($container);
        $containerCommandBus->register(AddDiningAreaCommand::class, AddDiningAreaHandler::class);
        $containerCommandBus->register(RemoveDiningAreaCommand::class, RemoveDiningAreaHandler::class);
        $containerCommandBus->register(UpdateDiningAreaCommand::class, UpdateDiningAreaHandler::class);
        $containerCommandBus->register(UpdateAvailabilitiesCommand::class, UpdateAvailabilitiesHandler::class);
        $containerCommandBus->register(UpdateSettingsCommand::class, UpdateSettingsHandler::class);
        $containerCommandBus->register(CreateNewRestaurantCommand::class, CreateNewRestaurantHandler::class);

        /** @var DeferredDomainEventBus $deferredDomainEventBus */
        $deferredDomainEventBus = $container->get(DeferredDomainEventBus::class);
        /** @var PdoUnitOfWork $pdoUnitOfWork */
        $pdoUnitOfWork = $container->get(PdoUnitOfWork::class);
        $domainEventFlushCommandBus = new DomainEventFlushCommandBus($containerCommandBus, $deferredDomainEventBus);
        $transactionalCommandBus = new TransactionalCommandBus($domainEventFlushCommandBus, $pdoUnitOfWork);
        $container->set(CommandBus::class, $transactionalCommandBus);

        $containerQueryBus = new ContainerQueryBus($container);
        $containerQueryBus->register(GetDiningAreaByIdQuery::class, GetDiningAreaByIdHandler::class);
        $containerQueryBus->register(GetRestaurantByIdQuery::class, GetRestaurantByIdHandler::class);
        $container->set(QueryBus::class, $containerQueryBus);

        BackgroundTasksDependencies::configure($container);

        // App Notification Services for Challenge Notifications (Using Background Tasks)
        $container->set(ChallengeNotificator::class, $container->get(BackgroundTaskChallengeNotificator::class));

        // Configure security: IdentityManager, repositories, use cases
        SecurityDependencies::configure($container);
    }
}
