<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Seedwork\Domain\DomainEvent;

final readonly class TimeSlotAssigned extends DomainEvent
{
    public static function new(string $restaurantId, Availability $availability): self
    {
        return new self(
            id: uniqid(),
            type: 'TimeSlotAssigned',
            payload: ['restaurantId' => $restaurantId, 'availability' => $availability]
        );
    }
}
