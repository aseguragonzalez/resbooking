<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Infrastructure;

use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Services\ChallengeNotificator;
use Psr\Log\LoggerInterface;

final readonly class ConsoleChallengeNotificator implements ChallengeNotificator
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void
    {
        $this->logger->info(
            "Sign-up challenge for {email}: Token={token}, ExpiresAt={expiresAt}",
            [
                'email' => $email,
                'token' => $challenge->getToken(),
                'expiresAt' => $challenge->expiresAt->format('c'),
            ]
        );
    }

    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void
    {
        $this->logger->info(
            "Reset password challenge for {email}: Token={token}, ExpiresAt={expiresAt}",
            [
                'email' => $email,
                'token' => $challenge->getToken(),
                'expiresAt' => $challenge->expiresAt->format('c'),
            ]
        );
    }
}
