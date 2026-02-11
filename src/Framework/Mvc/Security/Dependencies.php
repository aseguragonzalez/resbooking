<?php

declare(strict_types=1);

namespace Framework\Mvc\Security;

use DI\Container;
use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentityHandler;
use Framework\Mvc\Security\Application\DefaultIdentityManager;
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
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Mvc\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use Framework\Mvc\Security\Infrastructure\SqlResetPasswordChallengeRepository;
use Framework\Mvc\Security\Infrastructure\SqlSignInSessionRepository;
use Framework\Mvc\Security\Infrastructure\SqlSignUpChallengeRepository;
use Framework\Mvc\Security\Infrastructure\SqlUserIdentityRepository;

final class Dependencies
{
    public static function configure(Container $container): void
    {
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
