<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Domain\DomainEvent;

final readonly class TimeSlotAssigned extends DomainEvent
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
        Availability $availability,
        ?RestaurantEventId $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): self {
        $eventId = $id ?? RestaurantEventId::create();
        $eventCreatedAt = $createdAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return new self(
            $eventId,
            'time_slot.assigned',
            '1.0',
            [
                'restaurant_id' => $restaurantId->value,
                'day_of_week_id' => $availability->dayOfWeek->value,
                'time_slot_id' => $availability->timeSlot->value,
                'capacity' => $availability->capacity->value,
            ],
            $eventCreatedAt
        );
    }
}
