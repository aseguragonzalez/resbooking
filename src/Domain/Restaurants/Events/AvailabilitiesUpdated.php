<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Seedwork\Domain\EntityId;
use Seedwork\Domain\DomainEvent;

final readonly class AvailabilitiesUpdated extends DomainEvent
{
    /**
     * @param array<Availability> $availabilities
     */
    public static function new(EntityId $restaurantId, array $availabilities): self
    {
        return new self(
            id: EntityId::new(),
            type: 'AvailabilitiesUpdated',
            payload: ['restaurantId' => $restaurantId->value, 'availabilities' => $availabilities]
        );
    }
}
