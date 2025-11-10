<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Projects;

final readonly class Place
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $capacity,
        public string $name,
        public bool $available,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
