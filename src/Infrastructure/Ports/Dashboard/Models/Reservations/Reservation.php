<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations;

final class Reservation
{
    public function __construct(
        public readonly string $id,
        public readonly string $turn,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email
    ) {
    }
}
