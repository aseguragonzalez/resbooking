<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security;

use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;

interface IdentityStore
{
    public function deleteResetPasswordChallengeByToken(string $token): void;
    public function deleteSignInSessionByToken(string $token): void;
    public function deleteSignUpChallengeByToken(string $token): void;
    public function existsUserIdentityByUsername(string $username): bool;
    public function getResetPasswordChallengeByToken(string $token): ?ResetPasswordChallenge;
    public function getSignInSessionByToken(string $token): ?SignInSession;
    public function getSignUpChallengeByToken(string $token): ?SignUpChallenge;
    public function getUserIdentityByUsername(string $username): ?UserIdentity;
    public function saveResetPasswordChallenge(ResetPasswordChallenge $challenge): void;
    public function saveSignInSession(SignInSession $session): void;
    public function saveSignUpChallenge(SignUpChallenge $challenge): void;
    public function saveUserIdentity(UserIdentity $user): void;
}
