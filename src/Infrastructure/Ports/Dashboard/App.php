<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use DI\Container;
use Infrastructure\Adapters\Notificators\ConsoleChallengeNotificator;
use Infrastructure\Adapters\Repositories\IdentityStore\InFileIdentityStore;
use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Monolog\{Logger as MonoLogger, Level};
use Monolog\Handler\StreamHandler;
use Seedwork\Application\Logging\Logger;
use Monolog\Formatter\JsonFormatter;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\WebApp;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\ChallengesExpirationTime;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;
use Seedwork\Infrastructure\Logging\MonologLogger;

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
        $logger = new MonologLogger($logger, $context);
        $this->container->set(Logger::class, $logger);

        $challengeNotificator = new ConsoleChallengeNotificator($logger);
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
    }

    protected function router(): Router
    {
        return new Router(routes:[
            Route::create(RouteMethod::Get, Path::create('/'), DashboardController::class, 'index'),
            Route::create(RouteMethod::Get, Path::create('/reservations'), ReservationsController::class, 'index'),
            Route::create(
                RouteMethod::Get,
                Path::create('/reservations/create'),
                ReservationsController::class,
                'create'
            ),
            Route::create(RouteMethod::Get, Path::create('/reservations/{id}'), ReservationsController::class, 'edit'),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}'),
                ReservationsController::class,
                'update'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}/status'),
                ReservationsController::class,
                'updateStatus'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/sign-in'),
                AccountsController::class,
                'signIn'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/sign-in'),
                AccountsController::class,
                'signInUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/sign-up'),
                AccountsController::class,
                'signUp'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/sign-up'),
                AccountsController::class,
                'signUpUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/activate'),
                AccountsController::class,
                'activateUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/sign-out'),
                AccountsController::class,
                'signOut'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/reset-password'),
                AccountsController::class,
                'resetPassword'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/reset-password'),
                AccountsController::class,
                'sendResetPasswordEmail'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/reset-password-challenge'),
                AccountsController::class,
                'resetPasswordChallenge'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/reset-password-challenge'),
                AccountsController::class,
                'confirmResetPassword'
            ),
        ]);
    }
}
