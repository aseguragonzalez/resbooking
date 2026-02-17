<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class DiningAreaRemoved extends DomainEvent
{
    public static function new(EntityId $restaurantId, DiningArea $diningArea): self
    {
        return new self(
            id: EntityId::new(),
            type: 'DiningAreaRemoved',
            payload: ['restaurantId' => $restaurantId->value, 'diningArea' => $diningArea]
        );
    }
}
