<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Seedwork\Domain\Exceptions\ValueException;

final class Capacity
{
    public readonly int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new ValueException('Capacity must be greater than or equal to 0');
        }
        $this->value = $value;
    }

    public function equals(Capacity $other): bool
    {
        return $this->value === $other->value;
    }
}
