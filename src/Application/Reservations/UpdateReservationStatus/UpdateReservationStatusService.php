<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\ValueObjects\ReservationStatus;

final class UpdateReservationStatusService implements UpdateReservationStatus
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository
    ) {
    }

    public function execute(UpdateReservationStatusCommand $command): void
    {
        if (!$this->reservationRepository->exist($command->reservationId)) {
            throw new ReservationDoesNotExist();
        }

        $reservation = $this->reservationRepository->getById($command->reservationId);
        $status = ReservationStatus::from($command->status);
        $reservation->updateStatus($status);
        $this->reservationRepository->save($reservation);
    }
}
