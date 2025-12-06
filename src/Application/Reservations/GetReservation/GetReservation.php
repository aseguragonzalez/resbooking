<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

use Domain\Reservations\Entities\Reservation;

interface GetReservation
{
    public function execute(GetReservationCommand $command): Reservation;
}
