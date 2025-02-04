<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Shared;

readonly class Turn
{
    public function __construct(
        public int $id,
        public int $slotId,
        public string $start,
        public string $end,
    ) {
    }
}
