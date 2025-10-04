<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations\Requests;

final class UpdateReservationRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $backUrl,
        public readonly string $name = '',
        public readonly string $email = '',
        public readonly string $phone = '',
    ) {
    }
}
