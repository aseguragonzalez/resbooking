<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application;

use Framework\Module\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Module\Security\Application\ActivateUserIdentity\ActivateUserIdentityCommand;
use Framework\Module\Security\Application\GetIdentity\GetIdentity;
use Framework\Module\Security\Application\GetIdentity\GetIdentityCommand;
use Framework\Module\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Module\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordCommand;
use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSessionCommand;
use Framework\Module\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Module\Security\Application\RequestResetPassword\RequestResetPasswordCommand;
use Framework\Module\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Module\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenCommand;
use Framework\Module\Security\Application\SignIn\SignIn;
use Framework\Module\Security\Application\SignIn\SignInCommand;
use Framework\Module\Security\Application\SignOut\SignOut;
use Framework\Module\Security\Application\SignOut\SignOutCommand;
use Framework\Module\Security\Application\SignUp\SignUp;
use Framework\Module\Security\Application\SignUp\SignUpCommand;
use Framework\Module\Security\Challenge;
use Framework\Module\Security\Identity;
use Framework\Module\Security\IdentityManager;

final readonly class DefaultIdentityManager implements IdentityManager
{
    public function __construct(
        private SignUp $signUp,
        private ActivateUserIdentity $activateUserIdentity,
        private SignIn $signIn,
        private GetIdentity $getIdentity,
        private RefreshSignInSession $refreshSignInSession,
        private ModifyUserIdentityPassword $modifyUserIdentityPassword,
        private RequestResetPassword $requestResetPassword,
        private ResetPasswordFromToken $resetPasswordFromToken,
        private SignOut $signOut,
    ) {
    }

    /**
     * @param array<string> $roles
     */
    public function signUp(string $username, string $password, array $roles): void
    {
        $this->signUp->execute(new SignUpCommand($username, $password, $roles));
    }

    public function activateUserIdentity(string $token): void
    {
        $this->activateUserIdentity->execute(new ActivateUserIdentityCommand($token));
    }

    public function signIn(string $username, string $password, bool $keepMeSignedIn): Challenge
    {
        return $this->signIn->execute(new SignInCommand($username, $password, $keepMeSignedIn));
    }

    public function getIdentity(?string $token): Identity
    {
        return $this->getIdentity->execute(new GetIdentityCommand($token));
    }

    public function refreshSignInSession(string $token): Challenge
    {
        return $this->refreshSignInSession->execute(new RefreshSignInSessionCommand($token));
    }

    public function modifyUserIdentityPassword(string $token, string $currentPassword, string $newPassword): void
    {
        $this->modifyUserIdentityPassword->execute(
            new ModifyUserIdentityPasswordCommand($token, $currentPassword, $newPassword)
        );
    }

    public function resetPasswordChallenge(string $username): void
    {
        $this->requestResetPassword->execute(new RequestResetPasswordCommand($username));
    }

    public function resetPasswordFromToken(string $token, string $newPassword): void
    {
        $this->resetPasswordFromToken->execute(new ResetPasswordFromTokenCommand($token, $newPassword));
    }

    public function signOut(string $token): void
    {
        $this->signOut->execute(new SignOutCommand($token));
    }
}
