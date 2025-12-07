<?php

declare(strict_types=1);

namespace Domain\Projects\ValueObjects;

use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
use Seedwork\Domain\ValueObject;

final readonly class TurnAvailability extends ValueObject
{
    public function __construct(
        public Capacity $capacity,
        public DayOfWeek $dayOfWeek,
        public Turn $turn,
    ) {
    }

    public function equals(TurnAvailability $other): bool
    {
        return $this->dayOfWeek == $other->dayOfWeek && $this->turn == $other->turn;
    }
}
