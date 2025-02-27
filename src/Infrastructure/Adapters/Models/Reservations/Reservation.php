<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Reservations;

final readonly class Reservation
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $customerId,
        public int $placeId,
        public ?int $offerId,
        public int $stateId,
        public int $sourceId,
        public int $diners,
        public \DateTimeImmutable $date,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
