<?php

declare(strict_types=1);

namespace Domain\Reservations\Events;

use Domain\Reservations\Entities\Reservation;
use Seedwork\Domain\DomainEvent;

final class ReservationUpdated extends DomainEvent
{
    public static function new(string $reservationId, Reservation $reservation): self
    {
        return new self(
            id: uniqid(),
            type: 'ReservationUpdated',
            payload: ['reservationId' => $reservationId, 'reservation' => $reservation]
        );
    }
}
