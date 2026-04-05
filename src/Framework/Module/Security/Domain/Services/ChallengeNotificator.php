<?php

declare(strict_types=1);

namespace Framework\Security\Domain\Services;

use Framework\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Security\Domain\Entities\SignUpChallenge;

interface ChallengeNotificator
{
    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void;
    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void;
}
