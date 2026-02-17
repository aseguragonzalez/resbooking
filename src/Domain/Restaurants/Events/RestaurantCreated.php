<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\Restaurant;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class RestaurantCreated extends DomainEvent
{
    public static function new(EntityId $restaurantId, Restaurant $restaurant): self
    {
        return new self(
            id: EntityId::new(),
            type: 'RestaurantCreated',
            payload: ['restaurantId' => $restaurantId->value, 'restaurant' => $restaurant]
        );
    }
}
