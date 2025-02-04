<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

final class TurnItem
{
    public function __construct(
        public readonly int $capacity,
        public readonly int $dayOfWeek,
        public readonly string $startTime
    ) {
    }
}
