<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class TimeSlotAssigned extends DomainEvent
{
    public static function new(EntityId $restaurantId, Availability $availability): self
    {
        return new self(
            id: EntityId::new(),
            type: 'TimeSlotAssigned',
            payload: ['restaurantId' => $restaurantId->value, 'availability' => $availability]
        );
    }
}
