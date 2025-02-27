<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Projects;

final readonly class OpenCloseEvent
{
    public function __construct(
        public int $id,
        public int $projectId,
        public \DateTimeImmutable $date,
        public bool $isAvailable,
        public int $turnId,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
