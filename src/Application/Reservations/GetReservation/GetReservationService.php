<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Seedwork\Application\ApplicationService;

final class GetReservationService implements GetReservation
{
    public function __construct(private readonly ReservationRepository $reservationRepository)
    {
    }

    /**
     * @param GetReservationCommand $command
     * @return Reservation
     */
    public function execute($command): Reservation
    {
        if (!$this->reservationRepository->exist($command->reservationId)) {
            throw new ReservationDoesNotExist();
        }

        return $this->reservationRepository->getById($command->reservationId);
    }
}
