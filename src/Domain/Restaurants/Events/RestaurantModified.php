<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\Restaurant;
use Seedwork\Domain\DomainEvent;

final readonly class RestaurantModified extends DomainEvent
{
    public static function new(string $restaurantId, Restaurant $restaurant): self
    {
        return new self(
            id: uniqid(),
            type: 'RestaurantModified',
            payload: ['restaurantId' => $restaurantId, 'restaurant' => $restaurant]
        );
    }
}
