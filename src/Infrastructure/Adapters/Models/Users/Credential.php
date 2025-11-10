<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Users;

final readonly class Credential
{
    public function __construct(
        public int $userId,
        public string $secret,
        public string $seed,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
