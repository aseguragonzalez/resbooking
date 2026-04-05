<?php

declare(strict_types=1);

namespace Framework\Module\Security;

use DI\Container;
use Framework\Module\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Module\Security\Application\ActivateUserIdentity\ActivateUserIdentityHandler;
use Framework\Module\Security\Application\DefaultIdentityManager;
use Framework\Module\Security\Application\GetIdentity\GetIdentity;
use Framework\Module\Security\Application\GetIdentity\GetIdentityHandler;
use Framework\Module\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Module\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordHandler;
use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSessionHandler;
use Framework\Module\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Module\Security\Application\RequestResetPassword\RequestResetPasswordHandler;
use Framework\Module\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Module\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenHandler;
use Framework\Module\Security\Application\SignIn\SignIn;
use Framework\Module\Security\Application\SignIn\SignInHandler;
use Framework\Module\Security\Application\SignOut\SignOut;
use Framework\Module\Security\Application\SignOut\SignOutHandler;
use Framework\Module\Security\Application\SignUp\SignUp;
use Framework\Module\Security\Application\SignUp\SignUpHandler;
use Framework\Module\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Module\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Module\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Module\Security\Domain\Repositories\UserIdentityRepository;
use Framework\Module\Security\Infrastructure\SqlResetPasswordChallengeRepository;
use Framework\Module\Security\Infrastructure\SqlSignInSessionRepository;
use Framework\Module\Security\Infrastructure\SqlSignUpChallengeRepository;
use Framework\Module\Security\Infrastructure\SqlUserIdentityRepository;

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
