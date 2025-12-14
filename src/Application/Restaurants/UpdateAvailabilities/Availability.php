<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

final readonly class Availability
{
    public function __construct(
        public int $dayOfWeekId,
        public int $timeSlotId,
        public int $capacity,
    ) {
    }
}
