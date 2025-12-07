<?php

declare(strict_types=1);

namespace Application\Reservations\CreateReservation;

final readonly class CreateReservationCommand
{
    public function __construct(
        public string $projectId,
        public string $date,
        public int $turn,
        public string $name,
        public string $email,
        public string $phone,
        public int $numberOfDiners
    ) {
    }
}
