<?php

declare(strict_types=1);

namespace Domain\Shared;

use SeedWork\Domain\Exceptions\ValueException;
use SeedWork\Domain\ValueObject;

final readonly class Capacity extends ValueObject
{
    public function __construct(public int $value)
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        return $this->value === $other->value;
    }

    protected function validate(): void
    {
        if ($this->value < 0) {
            throw new ValueException('Capacity must be greater than or equal to 0');
        }
    }
}
