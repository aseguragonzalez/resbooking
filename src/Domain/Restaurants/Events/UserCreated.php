<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\User;
use Seedwork\Domain\DomainEvent;

final readonly class UserCreated extends DomainEvent
{
    public static function new(string $restaurantId, User $user): self
    {
        return new self(
            id: uniqid(),
            type: 'UserCreated',
            payload: ['restaurantId' => $restaurantId, 'user' => $user]
        );
    }
}
