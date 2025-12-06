<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

final class GetReservationCommand
{
    public function __construct(public readonly string $reservationId)
    {
    }
}
