<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\Restaurant;
use Seedwork\Domain\EntityId;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class RestaurantDeleted extends DomainEvent
{
    public static function new(EntityId $restaurantId, Restaurant $restaurant): self
    {
        return new self(
            id: EntityId::new(),
            type: 'RestaurantDeleted',
            payload: ['restaurantId' => $restaurantId->value, 'restaurant' => $restaurant]
        );
    }
}
