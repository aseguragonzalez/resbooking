<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;

final readonly class ListReservationsService implements ListReservations
{
    public function __construct(private ReservationRepository $reservationRepository)
    {
    }

    /**
     * @return array<Reservation>
     */
    public function execute(ListReservationsCommand $command): array
    {
        try {
            $fromDate = new \DateTimeImmutable($command->from);
        } catch (\Exception) {
            $fromDate = new \DateTimeImmutable('now');
        }

        $toDate = $fromDate->modify('+1 day');

        return $this->reservationRepository->findByRestaurantAndDateRange(
            restaurantId: $command->restaurantId,
            from: $fromDate,
            to: $toDate,
            offset: $command->offset,
            limit: 10
        );
    }
}
