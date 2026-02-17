<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\User;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class UserCreated extends DomainEvent
{
    public static function new(EntityId $restaurantId, User $user): self
    {
        return new self(
            id: EntityId::new(),
            type: 'UserCreated',
            payload: ['restaurantId' => $restaurantId->value, 'user' => $user]
        );
    }
}
