<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Turns;

final readonly class Turn
{
    public function __construct(
        public string $time,
        public int $dayOfWeekId,
        public int $turnId,
        public int $capacity,
    ) {
    }
}
