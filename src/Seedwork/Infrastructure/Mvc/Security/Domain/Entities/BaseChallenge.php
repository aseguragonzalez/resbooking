<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Entities;

use Seedwork\Infrastructure\Mvc\Security\Challenge;

abstract class BaseChallenge implements Challenge
{
    protected function __construct(public readonly string $token, public readonly \DateTimeImmutable $expiresAt)
    {
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
