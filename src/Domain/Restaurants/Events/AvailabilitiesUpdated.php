<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Seedwork\Domain\DomainEvent;

final readonly class AvailabilitiesUpdated extends DomainEvent
{
    /**
     * @param array<Availability> $availabilities
     */
    public static function new(string $restaurantId, array $availabilities): self
    {
        return new self(
            id: uniqid(),
            type: 'AvailabilitiesUpdated',
            payload: ['restaurantId' => $restaurantId, 'availabilities' => $availabilities]
        );
    }
}
