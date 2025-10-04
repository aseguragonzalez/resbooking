<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security;

final class ChallengesExpirationTime
{
    public function __construct(
        public readonly int $signUp = 60 * 24,
        public readonly int $signIn = 60,
        public readonly int $signInWithRememberMe = 60 * 24 * 30,
        public readonly int $refresh = 60,
        public readonly int $resetPasswordChallenge = 60 * 24,
    ) {
        if ($signUp <= 0) {
            throw new \InvalidArgumentException('signUp must be greater than 0');
        }
        if ($resetPasswordChallenge <= 0) {
            throw new \InvalidArgumentException('resetPasswordChallenge must be greater than 0');
        }
        if ($signIn <= 0) {
            throw new \InvalidArgumentException('signIn must be greater than 0');
        }
        if ($signInWithRememberMe <= 0) {
            throw new \InvalidArgumentException('signInWithRememberMe must be greater than 0');
        }
    }
}
