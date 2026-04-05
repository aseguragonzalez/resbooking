<?php

declare(strict_types=1);

namespace Framework\Security\Application;

use Framework\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Security\Application\ActivateUserIdentity\ActivateUserIdentityCommand;
use Framework\Security\Application\GetIdentity\GetIdentity;
use Framework\Security\Application\GetIdentity\GetIdentityCommand;
use Framework\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordCommand;
use Framework\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Security\Application\RefreshSignInSession\RefreshSignInSessionCommand;
use Framework\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Security\Application\RequestResetPassword\RequestResetPasswordCommand;
use Framework\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenCommand;
use Framework\Security\Application\SignIn\SignIn;
use Framework\Security\Application\SignIn\SignInCommand;
use Framework\Security\Application\SignOut\SignOut;
use Framework\Security\Application\SignOut\SignOutCommand;
use Framework\Security\Application\SignUp\SignUp;
use Framework\Security\Application\SignUp\SignUpCommand;
use Framework\Security\Challenge;
use Framework\Security\Identity;
use Framework\Security\IdentityManager;

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
