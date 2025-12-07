<?php

declare(strict_types=1);

namespace Domain\Reservations\Events;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Seedwork\Domain\DomainEvent;

final readonly class ReservationStatusUpdated extends DomainEvent
{
    public static function new(
        string $reservationId,
        Reservation $reservation,
        ReservationStatus $oldStatus,
        ReservationStatus $newStatus
    ): self {
        return new self(
            id: uniqid(),
            type: 'ReservationStatusUpdated',
            payload: [
                'reservationId' => $reservationId,
                'reservation' => $reservation,
                'oldStatus' => $oldStatus->value,
                'newStatus' => $newStatus->value
            ]
        );
    }
}
