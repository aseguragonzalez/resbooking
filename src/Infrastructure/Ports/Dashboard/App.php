<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Application\Projects\AddPlace\AddPlace;
use Application\Projects\AddPlace\AddPlaceService;
use Application\Projects\CreateNewProject\CreateNewProject;
use Application\Projects\CreateNewProject\CreateNewProjectService;
use Application\Projects\RemovePlace\RemovePlace;
use Application\Projects\RemovePlace\RemovePlaceService;
use Application\Projects\UpdatePlace\UpdatePlace;
use Application\Projects\UpdatePlace\UpdatePlaceService;
use Application\Projects\UpdateSettings\UpdateSettings;
use Application\Projects\UpdateSettings\UpdateSettingsService;
use Domain\Projects\ProjectRepository;
use Infrastructure\Adapters\Notificators\ConsoleChallengeNotificator;
use Infrastructure\Adapters\Repositories\IdentityStore\InFileIdentityStore;
use Infrastructure\Adapters\Repositories\Projects\InFileProjectRepository;
use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\PlacesController;
use Infrastructure\Ports\Dashboard\Controllers\ProjectController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Monolog\{Logger as MonoLogger, Level};
use Monolog\Handler\StreamHandler;
use Seedwork\Application\Logging\Logger;
use Monolog\Formatter\JsonFormatter;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\WebApp;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\ChallengesExpirationTime;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;
use Seedwork\Infrastructure\Logging\MonoLoggerAdapter;

final class App extends WebApp
{
    public function __construct(Container $container, Settings $settings)
    {
        parent::__construct($settings, $container);
    }

    protected function configure(): void
    {
        // read context data from the environment
        $context = [
            "service.name" => $this->settings->serviceName,
            "service.version" => $this->settings->serviceVersion,
            "environment" => $this->settings->environment,
        ];

        // configure logger
        $logger = new MonoLogger($context["service.name"]);
        $handler = new StreamHandler('php://stdout', Level::Debug);
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);
        $loggerAdapter = new MonoLoggerAdapter($logger, $context);
        $this->container->set(Logger::class, $loggerAdapter);

        $challengeNotificator = new ConsoleChallengeNotificator($loggerAdapter);
        $this->container->set(ChallengeNotificator::class, $challengeNotificator);
        /** @var InFileIdentityStore $inFileIdentityStore */
        $inFileIdentityStore = $this->container->get(InFileIdentityStore::class);
        $this->container->set(IdentityStore::class, $inFileIdentityStore);
        /** @var ChallengesExpirationTime $challengesExpirationTime */
        $challengesExpirationTime = $this->container->get(ChallengesExpirationTime::class);
        /** @var IdentityStore $identityStore */
        $identityStore = $this->container->get(IdentityStore::class);

        $defaultIdentityManager = new DefaultIdentityManager(
            notificator: $challengeNotificator,
            expirationTime: $challengesExpirationTime,
            store: $identityStore
        );
        $this->container->set(IdentityManager::class, $defaultIdentityManager);

        /** @var InFileProjectRepository $inFileProjectRepository */
        $inFileProjectRepository = $this->container->get(InFileProjectRepository::class);
        $this->container->set(ProjectRepository::class, $inFileProjectRepository);
        $createNewProjectService = $this->container->get(CreateNewProjectService::class);
        $this->container->set(CreateNewProject::class, $createNewProjectService);
        $updateSettingsService = $this->container->get(UpdateSettingsService::class);
        $this->container->set(UpdateSettings::class, $updateSettingsService);
        $addPlaceService = $this->container->get(AddPlaceService::class);
        $this->container->set(AddPlace::class, $addPlaceService);
        $removePlaceService = $this->container->get(RemovePlaceService::class);
        $this->container->set(RemovePlace::class, $removePlaceService);
        $updatePlaceService = $this->container->get(UpdatePlaceService::class);
        $this->container->set(UpdatePlace::class, $updatePlaceService);
    }

    protected function router(): Router
    {
        $accountsRoutes = AccountsController::getRoutes();
        $reservationsRoutes = ReservationsController::getRoutes();
        $dashboardRoutes = DashboardController::getRoutes();
        $projectRoutes = ProjectController::getRoutes();
        $placesRoutes = PlacesController::getRoutes();
        return new Router(routes:[
            ...$accountsRoutes,
            ...$reservationsRoutes,
            ...$dashboardRoutes,
            ...$projectRoutes,
            ...$placesRoutes
        ]);
    }
}
