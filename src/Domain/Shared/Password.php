<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Password
{
    public const MIN_LENGTH = 8;

    public function __construct(private readonly string $value)
    {
        if (strlen($value) < self::MIN_LENGTH) {
            throw new \InvalidArgumentException('Password must be at least 8 characters');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Password $password): bool
    {
        return $this->value === $password->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
