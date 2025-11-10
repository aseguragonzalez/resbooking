<?php

declare(strict_types=1);

namespace Domain\Shared\ValueObjects;

use Domain\Shared\{DayOfWeek, Capacity, Turn};
use Seedwork\Domain\ValueObject;

final class TurnAvailability extends ValueObject
{
    public function __construct(
        public readonly Capacity $capacity,
        public readonly DayOfWeek $dayOfWeek,
        public readonly Turn $turn,
    ) {
    }

    public function equals(TurnAvailability $other): bool
    {
        return $this->dayOfWeek == $other->dayOfWeek && $this->turn == $other->turn;
    }
}
