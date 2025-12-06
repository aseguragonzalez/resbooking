<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<UpdateReservationStatusCommand>
 */
final class UpdateReservationStatusService extends ApplicationService implements UpdateReservationStatus
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository
    ) {
    }

    /**
     * @param UpdateReservationStatusCommand $command
     */
    public function execute($command): void
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
