<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;

final class ListReservationsService implements ListReservations
{
    public function __construct(private readonly ReservationRepository $reservationRepository)
    {
    }

    /**
     * @param ListReservationsCommand $command
     * @return array<Reservation>
     */
    public function execute($command): array
    {
        try {
            $fromDate = new \DateTimeImmutable($command->from);
        } catch (\Exception) {
            $fromDate = new \DateTimeImmutable('now');
        }

        $toDate = $fromDate->modify('+1 day');

        return $this->reservationRepository->findByProjectAndDateRange(
            projectId: $command->projectId,
            from: $fromDate,
            to: $toDate,
            offset: $command->offset,
            limit: 10
        );
    }
}
