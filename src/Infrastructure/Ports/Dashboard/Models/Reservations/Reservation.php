<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations;

final readonly class Reservation
{
    public function __construct(
        public string $id,
        public string $turn,
        public string $name,
        public string $phone,
        public string $email
    ) {
    }
}
