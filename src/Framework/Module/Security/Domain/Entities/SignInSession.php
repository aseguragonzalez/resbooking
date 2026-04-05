<?php

declare(strict_types=1);

namespace Framework\Module\Security\Domain\Entities;

use Framework\Module\Security\Challenge;
use Framework\Module\Security\Identity;

final readonly class SignInSession
{
    private function __construct(public Challenge $challenge, public Identity $identity)
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
