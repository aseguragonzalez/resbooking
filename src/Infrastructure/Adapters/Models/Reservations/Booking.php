<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Booking
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $idTurn,
        public int $idClient,
        public string $date,
        public int $diners,
        public string $clientName,
        public string $email,
        public string $phone,
        public string $createDate,
        public int $state,
        public int $idOffer,
        public int $idPlace,
        public string $comment,
        public int $idBookingSource,
        public string $notes,
        public string $preOrder,
        public string $table,
    ) {
    }
}
