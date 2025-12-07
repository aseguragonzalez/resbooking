<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

final readonly class UpdateReservationCommand
{
    public function __construct(
        public string $reservationId,
        public string $name,
        public string $email,
        public string $phone
    ) {
    }
}
