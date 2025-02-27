<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Projects;

final readonly class Project
{
    public function __construct(
        public int $id,
        public bool $available,
        public string $email,
        public bool $hasRemainders,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public string $name,
        public int $numberOfTables,
        public ?string $phone,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
