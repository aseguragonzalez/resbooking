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
use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentityHandler;
use Framework\Mvc\Security\Application\GetIdentity\GetIdentity;
use Framework\Mvc\Security\Application\GetIdentity\GetIdentityHandler;
use Framework\Mvc\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Mvc\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordHandler;
use Framework\Mvc\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Mvc\Security\Application\RefreshSignInSession\RefreshSignInSessionHandler;
use Framework\Mvc\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Mvc\Security\Application\RequestResetPassword\RequestResetPasswordHandler;
use Framework\Mvc\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Mvc\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenHandler;
use Framework\Mvc\Security\Application\SignIn\SignIn;
use Framework\Mvc\Security\Application\SignIn\SignInHandler;
use Framework\Mvc\Security\Application\SignOut\SignOut;
use Framework\Mvc\Security\Application\SignOut\SignOutHandler;
use Framework\Mvc\Security\Application\SignUp\SignUp;
use Framework\Mvc\Security\Application\SignUp\SignUpHandler;
use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Mvc\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use Framework\Mvc\Security\IdentityManager;
use Infrastructure\Adapters\Notificators\ConsoleChallengeNotificator;
use Infrastructure\Adapters\Repositories\Identity\SqlResetPasswordChallengeRepository;
use Infrastructure\Adapters\Repositories\Identity\SqlSignInSessionRepository;
use Infrastructure\Adapters\Repositories\Identity\SqlSignUpChallengeRepository;
use Infrastructure\Adapters\Repositories\Identity\SqlUserIdentityRepository;
use Infrastructure\Adapters\Repositories\Restaurants\SqlRestaurantRepository;

final class Dependencies
{
    public static function configure(Container $container): void
    {
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
        $container->set(ChallengeNotificator::class, $container->get(ConsoleChallengeNotificator::class));
        $container->set(UserIdentityRepository::class, $container->get(SqlUserIdentityRepository::class));
        $container->set(SignInSessionRepository::class, $container->get(SqlSignInSessionRepository::class));
        $container->set(SignUpChallengeRepository::class, $container->get(SqlSignUpChallengeRepository::class));
        $container->set(
            ResetPasswordChallengeRepository::class,
            $container->get(SqlResetPasswordChallengeRepository::class)
        );

        $container->set(SignUp::class, $container->get(SignUpHandler::class));
        $container->set(ActivateUserIdentity::class, $container->get(ActivateUserIdentityHandler::class));
        $container->set(SignIn::class, $container->get(SignInHandler::class));
        $container->set(GetIdentity::class, $container->get(GetIdentityHandler::class));
        $container->set(RefreshSignInSession::class, $container->get(RefreshSignInSessionHandler::class));
        $container->set(ModifyUserIdentityPassword::class, $container->get(ModifyUserIdentityPasswordHandler::class));
        $container->set(RequestResetPassword::class, $container->get(RequestResetPasswordHandler::class));
        $container->set(ResetPasswordFromToken::class, $container->get(ResetPasswordFromTokenHandler::class));
        $container->set(SignOut::class, $container->get(SignOutHandler::class));

        $container->set(IdentityManager::class, $container->get(DefaultIdentityManager::class));
    }
}
