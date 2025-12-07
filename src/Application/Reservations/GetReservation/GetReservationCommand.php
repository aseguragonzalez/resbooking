<?php

declare(strict_types=1);

namespace Application\Reservations\GetReservation;

final readonly class GetReservationCommand
{
    public function __construct(public string $reservationId)
    {
    }
}
