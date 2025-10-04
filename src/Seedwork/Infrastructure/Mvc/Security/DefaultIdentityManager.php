<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security;

use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\UserIsNotFoundException;

final class DefaultIdentityManager implements IdentityManager
{
    public function __construct(
        private readonly ChallengeNotificator $notificator,
        private readonly ChallengesExpirationTime $expirationTime,
        private readonly IdentityStore $store,
    ) {
    }

    /**
     * @param array<string> $roles
     */
    public function signUp(string $username, string $password, array $roles): void
    {
        if ($this->store->existsUserIdentityByUsername($username)) {
            return;
        }

        $user = UserIdentity::new($username, $roles, $password);
        $this->store->saveUserIdentity($user);
        $challenge = SignUpChallenge::new($this->expiresAt($this->expirationTime->signUp), $user);
        $this->store->saveSignUpChallenge($challenge);
        $this->notificator->sendSignUpChallenge($username, $challenge);
    }

    public function activateUserIdentity(string $token): void
    {
        $challenge = $this->store->getSignUpChallengeByToken($token);
        if ($challenge === null) {
            throw new SignUpChallengeException($token);
        }

        if ($challenge->isExpired()) {
            $this->store->deleteSignUpChallengeByToken($token);
            throw new SignUpChallengeException($token);
        }

        $this->store->saveUserIdentity($challenge->userIdentity->activate());
        $this->store->deleteSignUpChallengeByToken($token);
    }

    public function signIn(string $username, string $password, bool $keepMeSignedIn): Challenge
    {
        $user = $this->store->getUserIdentityByUsername($username);
        if ($user === null) {
            throw new InvalidCredentialsException($username);
        }

        $authenticatedUser = $user->authenticate($password);

        $expiresAt = $keepMeSignedIn
            ? $this->expiresAt($this->expirationTime->signInWithRememberMe)
            : $this->expiresAt($this->expirationTime->signIn);

        $session = SignInSession::new($expiresAt, $authenticatedUser);
        $this->store->saveSignInSession($session);
        return $session->challenge;
    }

    public function getIdentity(?string $token): Identity
    {
        if (!isset($token) || empty($token) || empty(trim($token))) {
            return UserIdentity::anonymous();
        }

        $session = $this->getSignInSessionOrFail($token);
        return $session->identity;
    }

    public function refreshSignInSession(string $token): Challenge
    {
        $session = $this->getSignInSessionOrFail($token);
        $sessionUpdated = $session->refreshUntil($this->expiresAt($this->expirationTime->refresh));
        $this->store->saveSignInSession($sessionUpdated);
        return $sessionUpdated->challenge;
    }

    private function getSignInSessionOrFail(string $token): SignInSession
    {
        $session = $this->store->getSignInSessionByToken($token);
        if ($session === null) {
            throw new SessionExpiredException();
        }

        if ($session->isExpired()) {
            $this->store->deleteSignInSessionByToken($token);
            throw new SessionExpiredException();
        }

        return $session;
    }

    public function modifyUserIdentityPassword(string $token, string $currentPassword, string $newPassword): void
    {
        $session = $this->getSignInSessionOrFail($token);
        $user = $this->store->getUserIdentityByUsername($session->identity->username());
        if ($user === null) {
            throw new UserIsNotFoundException($session->identity->username());
        }
        $user->validatePassword($currentPassword);
        $this->store->saveUserIdentity($user->updatePassword($newPassword));
    }

    public function resetPasswordChallenge(string $username): void
    {
        $user = $this->store->getUserIdentityByUsername($username);
        if ($user === null) {
            return;
        }

        $challenge = ResetPasswordChallenge::new(
            $this->expiresAt($this->expirationTime->resetPasswordChallenge),
            $user
        );
        $this->store->saveResetPasswordChallenge($challenge);
        $this->notificator->sendResetPasswordChallenge($username, $challenge);
    }

    public function resetPasswordFromToken(string $token, string $newPassword): void
    {
        $resetPasswordChallenge = $this->store->getResetPasswordChallengeByToken($token);
        if ($resetPasswordChallenge === null) {
            return;
        }

        if ($resetPasswordChallenge->isExpired()) {
            $this->store->deleteResetPasswordChallengeByToken($token);
            throw new ResetPasswordChallengeException($token);
        }

        $user = $this->store->getUserIdentityByUsername($resetPasswordChallenge->userIdentity->username());
        if ($user === null) {
            return;
        }

        $this->store->saveUserIdentity($user->updatePassword($newPassword));
    }

    public function signOut(string $token): void
    {
        $this->store->deleteSignInSessionByToken($token);
    }

    private function expiresAt(int $minutes): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->modify("+{$minutes} minutes");
    }
}
