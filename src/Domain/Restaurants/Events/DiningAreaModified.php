<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Seedwork\Domain\EntityId;
use Seedwork\Domain\DomainEvent;

final readonly class DiningAreaModified extends DomainEvent
{
    public static function new(EntityId $restaurantId, DiningArea $diningArea): self
    {
        return new self(
            id: EntityId::new(),
            type: 'DiningAreaModified',
            payload: ['restaurantId' => $restaurantId->value, 'diningArea' => $diningArea]
        );
    }
}
