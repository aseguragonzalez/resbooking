<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Turn
{
    public function __construct(
        public int $projectId,
        public int $id,
        public int $dayOfWeekId,
        public int $slotId,
        public string $start,
        public string $end,
    ) { }
}
