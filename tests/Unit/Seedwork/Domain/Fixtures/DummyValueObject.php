<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\ValueObject;

final readonly class DummyValueObject extends ValueObject
{
    public function __construct(private string $value)
    {
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        return $this->value === $other->value;
    }
}
