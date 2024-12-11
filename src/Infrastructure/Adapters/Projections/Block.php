<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Block
{
    public function __construct(
        public int $id,
        public int $year,
        public int $week,
        public int $dayOfWeekId,
        public int $turnId,
        public string $date,
        public bool $block,
    ) { }
}
