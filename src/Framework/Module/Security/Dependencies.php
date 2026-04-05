<?php

declare(strict_types=1);

namespace Framework\Security;

use DI\Container;
use Framework\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Security\Application\ActivateUserIdentity\ActivateUserIdentityHandler;
use Framework\Security\Application\DefaultIdentityManager;
use Framework\Security\Application\GetIdentity\GetIdentity;
use Framework\Security\Application\GetIdentity\GetIdentityHandler;
use Framework\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordHandler;
use Framework\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Security\Application\RefreshSignInSession\RefreshSignInSessionHandler;
use Framework\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Security\Application\RequestResetPassword\RequestResetPasswordHandler;
use Framework\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenHandler;
use Framework\Security\Application\SignIn\SignIn;
use Framework\Security\Application\SignIn\SignInHandler;
use Framework\Security\Application\SignOut\SignOut;
use Framework\Security\Application\SignOut\SignOutHandler;
use Framework\Security\Application\SignUp\SignUp;
use Framework\Security\Application\SignUp\SignUpHandler;
use Framework\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Security\Domain\Repositories\UserIdentityRepository;
use Framework\Security\Infrastructure\SqlResetPasswordChallengeRepository;
use Framework\Security\Infrastructure\SqlSignInSessionRepository;
use Framework\Security\Infrastructure\SqlSignUpChallengeRepository;
use Framework\Security\Infrastructure\SqlUserIdentityRepository;

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
