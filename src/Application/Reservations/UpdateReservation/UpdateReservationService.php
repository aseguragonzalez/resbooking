<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<UpdateReservationCommand>
 */
final class UpdateReservationService extends ApplicationService implements UpdateReservation
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository
    ) {
    }

    /**
     * @param UpdateReservationCommand $command
     */
    public function execute($command): void
    {
        if (!$this->reservationRepository->exist($command->reservationId)) {
            throw new ReservationDoesNotExist();
        }

        $reservation = $this->reservationRepository->getById($command->reservationId);

        $email = new Email($command->email);
        $phone = new Phone($command->phone);

        $reservation->updateDetails(
            name: $command->name,
            email: $email,
            phone: $phone
        );

        $this->reservationRepository->save($reservation);
    }
}
