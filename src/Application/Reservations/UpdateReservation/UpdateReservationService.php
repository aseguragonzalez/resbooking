<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\Services\ReservationObtainer;
use Domain\Shared\Email;
use Domain\Shared\Phone;

final readonly class UpdateReservationService implements UpdateReservation
{
    public function __construct(
        private ReservationObtainer $reservationObtainer,
        private ReservationRepository $reservationRepository,
    ) {
    }

    public function execute(UpdateReservationCommand $command): void
    {
        $reservation = $this->reservationObtainer->obtain($command->reservationId);

        $reservation->updateDetails(
            name: $command->name,
            email: new Email($command->email),
            phone: new Phone($command->phone),
        );

        $this->reservationRepository->save($reservation);
    }
}
