<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security;

use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;

interface ChallengeNotificator
{
    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void;
    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void;
}
