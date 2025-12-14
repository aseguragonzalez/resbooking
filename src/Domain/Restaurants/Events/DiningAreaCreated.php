<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Seedwork\Domain\DomainEvent;

final readonly class DiningAreaCreated extends DomainEvent
{
    public static function new(string $restaurantId, DiningArea $diningArea): self
    {
        return new self(
            id: uniqid(),
            type: 'DiningAreaCreated',
            payload: ['restaurantId' => $restaurantId, 'diningArea' => $diningArea]
        );
    }
}
