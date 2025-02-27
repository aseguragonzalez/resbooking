<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Reservations;

final readonly class Customer
{
    public function __construct(
        public int $id,
        public int $projectId,
        public bool $available,
        public bool $shared,
        public string $email,
        public string $name,
        public string $phone,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
