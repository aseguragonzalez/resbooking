<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

final readonly class AvailabilityItem
{
    public function __construct(
        public string $time,
        public int $dayOfWeekId,
        public int $timeSlotId,
        public int $capacity,
    ) {
    }
}
