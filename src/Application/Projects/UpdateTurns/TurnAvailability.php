<?php

declare(strict_types=1);

namespace Application\Projects\UpdateTurns;

final readonly class TurnAvailability
{
    public function __construct(
        public int $dayOfWeekId,
        public int $turnId,
        public int $capacity,
    ) {
    }
}
