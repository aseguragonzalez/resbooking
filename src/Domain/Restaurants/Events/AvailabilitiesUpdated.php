<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Domain\DomainEvent;

final readonly class AvailabilitiesUpdated extends DomainEvent
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

    /**
     * @param array<Availability> $availabilities
     */
    public static function create(
        RestaurantId $restaurantId,
        array $availabilities,
        ?RestaurantEventId $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): self {
        $eventId = $id ?? RestaurantEventId::create();
        $eventCreatedAt = $createdAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $serialized = array_map(
            static fn (Availability $a): array => [
                'day_of_week_id' => $a->dayOfWeek->value,
                'time_slot_id' => $a->timeSlot->value,
                'capacity' => $a->capacity->value,
            ],
            $availabilities
        );
        return new self(
            $eventId,
            'availabilities.updated',
            '1.0',
            [
                'restaurant_id' => $restaurantId->value,
                'availabilities' => $serialized,
            ],
            $eventCreatedAt
        );
    }
}
