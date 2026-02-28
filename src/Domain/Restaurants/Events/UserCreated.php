<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\User;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Domain\DomainEvent;

final readonly class UserCreated extends DomainEvent
{
    private function __construct(
        RestaurantEventId $id,
        string $type,
        string $version,
        array $payload,
        \DateTimeImmutable $createdAt
    ) {
        parent::__construct($id, $type, $version, $payload, $createdAt);
    }

    public static function create(
        RestaurantId $restaurantId,
        User $user,
        ?RestaurantEventId $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): self {
        $eventId = $id ?? RestaurantEventId::create();
        $eventCreatedAt = $createdAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return new self(
            $eventId,
            'user.created',
            '1.0',
            [
                'restaurant_id' => $restaurantId->value,
                'user_email' => $user->username->value,
            ],
            $eventCreatedAt
        );
    }
}
