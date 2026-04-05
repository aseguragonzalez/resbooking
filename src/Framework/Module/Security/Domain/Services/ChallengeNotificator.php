<?php

declare(strict_types=1);

namespace Framework\Module\Security\Domain\Services;

use Framework\Module\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Module\Security\Domain\Entities\SignUpChallenge;

interface ChallengeNotificator
{
    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void;
    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void;
}
