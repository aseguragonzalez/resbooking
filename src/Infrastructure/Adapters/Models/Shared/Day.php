<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Shared;

readonly class Day
{
    public function __construct(
        public int $id,
        public string $name,
        public int $dayOfWeek,
    ) { }
}
