<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

use Seedwork\Application\Command;

final class UpdateReservationStatusCommand extends Command
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $status
    ) {
    }
}
