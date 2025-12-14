<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Availabilities;

final readonly class Availability
{
    public string $id;

    public function __construct(
        public string $time,
        public int $dayOfWeekId,
        public int $timeSlotId,
        public int $capacity,
    ) {
        $this->id = "{$this->timeSlotId}_{$this->dayOfWeekId}";
    }
}
