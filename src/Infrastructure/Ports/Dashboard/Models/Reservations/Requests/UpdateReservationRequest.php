<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations\Requests;

final readonly class UpdateReservationRequest
{
    public function __construct(
        public string $id,
        public string $backUrl,
        public string $name = '',
        public string $email = '',
        public string $phone = '',
    ) {
    }
}
