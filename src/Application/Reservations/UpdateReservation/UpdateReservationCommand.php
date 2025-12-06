<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

final class UpdateReservationCommand
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone
    ) {
    }
}
