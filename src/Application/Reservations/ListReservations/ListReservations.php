<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

use Domain\Reservations\Entities\Reservation;

interface ListReservations
{
    /**
     * @return array<Reservation>
     */
    public function execute(ListReservationsCommand $command): array;
}
