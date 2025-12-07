<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Services\ReservationObtainer;

final readonly class GetReservationService implements GetReservation
{
    public function __construct(
        private ReservationObtainer $reservationObtainer,
    ) {
    }

    public function execute(GetReservationCommand $command): Reservation
    {
        return $this->reservationObtainer->obtain($command->reservationId);
    }
}
