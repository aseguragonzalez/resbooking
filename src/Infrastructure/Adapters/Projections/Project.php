<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Project
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $path,
        public string $date,
        public int $serviceId,
        public int $userId,
        public string $username,
        public bool $active,
    ) { }
}
