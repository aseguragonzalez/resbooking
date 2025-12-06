<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

use Seedwork\Application\Command;

final class UpdateReservationCommand extends Command
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone
    ) {
    }
}
