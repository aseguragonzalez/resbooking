<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class TurnShare
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $turnId,
        public int $dayOfWeekId,
        public int $share,
    ) { }
}
