<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;

final readonly class GetReservationService implements GetReservation
{
    public function __construct(private ReservationRepository $reservationRepository)
    {
    }

    public function execute(GetReservationCommand $command): Reservation
    {
        if (!$this->reservationRepository->exist($command->reservationId)) {
            throw new ReservationDoesNotExist();
        }

        return $this->reservationRepository->getById($command->reservationId);
    }
}
