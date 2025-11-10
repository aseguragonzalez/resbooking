<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Notificators;

use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;

final class ConsoleChallengeNotificator implements ChallengeNotificator
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void
    {
        $this->logger->info(
            "Sign-up challenge for {$email}: Token={$challenge->getToken()}," .
            "ExpiresAt={$challenge->expiresAt->format('c')}"
        );
    }

    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void
    {
        $this->logger->info(
            "Reset password challenge for {$email}: Token={$challenge->getToken()}," .
            "ExpiresAt={$challenge->expiresAt->format('c')}"
        );
    }
}
