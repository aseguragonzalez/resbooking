<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Projects;

final readonly class TurnAvailability
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $capacity,
        public int $dayOfWeekId,
        public int $turnId,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
