<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Users;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $username,
        public string $secret,
        public string $seed,
        public bool $locked,
        public bool $available,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
