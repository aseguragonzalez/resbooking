<?php

declare(strict_types=1);

namespace Domain\Reservations\ValueObjects;

enum ReservationStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case CANCELLED = 'CANCELLED';
}
