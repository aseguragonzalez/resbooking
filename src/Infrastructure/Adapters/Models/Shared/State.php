<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Shared;

readonly class State
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public int $level,
    ) {
    }
}
