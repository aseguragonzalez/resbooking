<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

final readonly class UpdateReservationStatusCommand
{
    public function __construct(
        public string $reservationId,
        public string $status
    ) {
    }
}
