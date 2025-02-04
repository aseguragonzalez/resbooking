<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class BookingNotification
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $start,
        public string $date,
        public int $diners,
        public string $name,
        public string $email,
        public string $phone,
        public ?int $state,
        public string $place,
        public string $title,
        public string $description,
        public string $terms,
        public string $comment,
        public string $notes,
        public string $preOrder,
        public string $qrContent,
    ) {
    }
}
