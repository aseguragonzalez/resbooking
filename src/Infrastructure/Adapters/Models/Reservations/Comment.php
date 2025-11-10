<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Reservations;

final readonly class Comment
{
    public function __construct(
        public int $id,
        public int $reservationId,
        public ?int $userId,
        public ?int $customerId,
        public string $content,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }
}
