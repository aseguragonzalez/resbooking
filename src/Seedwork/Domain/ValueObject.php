<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class ValueObject
{
    public function equals(ValueObject $other): bool
    {
        return $this == $other;
    }
}
