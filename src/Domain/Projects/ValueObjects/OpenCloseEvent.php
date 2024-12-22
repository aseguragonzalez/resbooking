<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use DateTimeInmutable;
use App\Domain\Shared\Turn;
use App\Seedwork\Domain\ValueObject;

final class OpenCloseEvent extends ValueObject
{
    public function __construct(
        public readonly DateTimeInmutable $date,
        public readonly bool $isAvailable,
        public readonly Turn $turn,
    ) { }

    public function equals(OpenCloseEvent $other): bool
    {
        return $this->turn->equals($other->turn)
            && $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }
}
