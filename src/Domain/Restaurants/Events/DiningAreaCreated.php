<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Domain\DomainEvent;

final readonly class DiningAreaCreated extends DomainEvent
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
        DiningArea $diningArea,
        ?RestaurantEventId $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): self {
        $eventId = $id ?? RestaurantEventId::create();
        $eventCreatedAt = $createdAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return new self(
            $eventId,
            'dining_area.created',
            '1.0',
            [
                'restaurant_id' => $restaurantId->value,
                'dining_area_id' => $diningArea->id->value,
                'name' => $diningArea->name,
                'capacity' => $diningArea->capacity->value,
            ],
            $eventCreatedAt
        );
    }
}
