<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Project
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $path,
        public string $date,
        public bool $active,
    ) {
    }
}
