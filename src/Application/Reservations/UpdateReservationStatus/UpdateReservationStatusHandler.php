<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\Services\ReservationObtainer;
use Domain\Reservations\ValueObjects\ReservationStatus;

final readonly class UpdateReservationStatusHandler implements UpdateReservationStatus
{
    public function __construct(
        private ReservationObtainer $reservationObtainer,
        private ReservationRepository $reservationRepository,
    ) {
    }

    public function execute(UpdateReservationStatusCommand $command): void
    {
        $reservation = $this->reservationObtainer->obtain($command->reservationId);
        $status = ReservationStatus::from($command->status);
        $reservation->updateStatus($status);
        $this->reservationRepository->save($reservation);
    }
}
