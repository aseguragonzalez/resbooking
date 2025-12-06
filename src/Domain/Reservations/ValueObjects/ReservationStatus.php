<?php

declare(strict_types=1);

namespace Domain\Reservations\ValueObjects;

enum ReservationStatus: string
{
    case Pending = 'PENDING';
    case Accepted = 'ACCEPTED';
    case Cancelled = 'CANCELLED';
}
