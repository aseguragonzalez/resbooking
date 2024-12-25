<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Place
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $name,
        public string $description,
        public int $size,
        public bool $active,
    ) {
    }
}
