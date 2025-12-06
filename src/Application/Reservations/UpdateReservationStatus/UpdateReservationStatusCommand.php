<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

final class UpdateReservationStatusCommand
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $status
    ) {
    }
}
