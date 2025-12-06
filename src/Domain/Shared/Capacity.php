<?php

declare(strict_types=1);

namespace Domain\Shared;

final class Capacity
{
    public function __construct(public readonly int $value)
    {
        if ($this->value < 0) {
            throw new \InvalidArgumentException('Capacity must be greater than or equal to 0');
        }
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function equals(Capacity $other): bool
    {
        return $this->value === $other->value;
    }
}
