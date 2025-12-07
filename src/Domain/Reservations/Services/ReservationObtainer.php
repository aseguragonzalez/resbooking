<?php

declare(strict_types=1);

namespace Domain\Reservations\Services;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;

readonly class ReservationObtainer
{
    public function __construct(private ReservationRepository $reservationRepository)
    {
    }

    public function obtain(string $id): Reservation
    {
        $reservation = $this->reservationRepository->getById($id);
        if ($reservation === null) {
            throw new ReservationDoesNotExist();
        }
        return $reservation;
    }
}
