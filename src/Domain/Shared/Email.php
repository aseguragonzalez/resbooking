<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Email
{
    public function __construct(private readonly string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->email;
    }

    public function value(): string
    {
        return $this->email;
    }
}
