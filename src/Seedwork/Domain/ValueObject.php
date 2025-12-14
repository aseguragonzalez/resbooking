<?php

declare(strict_types=1);

namespace Seedwork\Domain;

abstract readonly class ValueObject
{
    abstract public function equals(ValueObject $other): bool;
}
