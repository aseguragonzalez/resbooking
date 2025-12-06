<?php

declare(strict_types=1);

namespace Application\Reservations\CreateReservation;

final class CreateReservationCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $date,
        public readonly int $turn,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly int $numberOfDiners
    ) {
    }
}
