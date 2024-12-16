<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
use App\Seedwork\Domain\ValueObject;

// TODO: find a better name for this class
final class AvailabilityTurn extends ValueObject
{
    public function __construct(
        public readonly Capacity $capacity,
        public readonly DayOfWeek $dayOfWeek,
        public readonly Turn $turn,
    ) { }

    public function equals(AvailabilityTurn $other): bool
    {
        return $this->dayOfWeek->equals($other->dayOfWeek) && $this->turn->equals($other->turn);
    }
}
