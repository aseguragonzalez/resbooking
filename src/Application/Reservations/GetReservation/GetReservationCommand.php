<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

use Seedwork\Application\Command;

final class GetReservationCommand extends Command
{
    public function __construct(public readonly string $reservationId)
    {
    }
}
