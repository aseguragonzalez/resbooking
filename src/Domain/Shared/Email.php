<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Email
{
    public function __construct(private readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $email): bool
    {
        return $this->value === $email->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
