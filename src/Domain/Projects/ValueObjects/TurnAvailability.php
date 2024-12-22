<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
use App\Seedwork\Domain\ValueObject;

final class TurnAvailability extends ValueObject
{
    public function __construct(
        public readonly Capacity $capacity,
        public readonly DayOfWeek $dayOfWeek,
        public readonly Turn $turn,
    ) { }

    public function equals(TurnAvailability $other): bool
    {
        return $this->dayOfWeek->equals($other->dayOfWeek) && $this->turn->equals($other->turn);
    }
}
