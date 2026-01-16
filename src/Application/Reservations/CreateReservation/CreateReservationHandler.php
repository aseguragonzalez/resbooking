<?php

declare(strict_types=1);

namespace Application\Reservations\CreateReservation;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;

final readonly class CreateReservationHandler implements CreateReservation
{
    public function __construct(
        private ReservationRepository $reservationRepository
    ) {
    }

    public function execute(CreateReservationCommand $command): void
    {
        try {
            $date = new \DateTimeImmutable($command->date);
        } catch (\Exception) {
            $date = new \DateTimeImmutable('now');
        }

        $turn = TimeSlot::getById($command->turn);
        $email = new Email($command->email);
        $phone = new Phone($command->phone);
        $numberOfDiners = new Capacity($command->numberOfDiners);

        $reservation = Reservation::new(
            restaurantId: $command->restaurantId,
            date: $date,
            turn: $turn,
            name: $command->name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners
        );

        $this->reservationRepository->save($reservation);
    }
}
