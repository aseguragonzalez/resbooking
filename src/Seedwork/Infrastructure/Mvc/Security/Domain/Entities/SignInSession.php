<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Entities;

use Seedwork\Infrastructure\Mvc\Security\Challenge;
use Seedwork\Infrastructure\Mvc\Security\Identity;

final class SignInSession
{
    private function __construct(public readonly Challenge $challenge, public readonly Identity $identity)
    {
    }

    public function isExpired(): bool
    {
        return $this->challenge->isExpired();
    }

    public function refreshUntil(\DateTimeImmutable $dateTime): self
    {
        return new self($this->challenge->refreshUntil($dateTime), $this->identity);
    }

    public static function new(\DateTimeImmutable $expiredAt, Identity $identity): self
    {
        return new self(SignInChallenge::new($expiredAt), $identity);
    }

    public static function build(Challenge $challenge, Identity $identity): self
    {
        return new self($challenge, $identity);
    }
}
