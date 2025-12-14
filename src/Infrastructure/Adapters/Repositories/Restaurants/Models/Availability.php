<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants\Models;

final readonly class Availability implements \JsonSerializable
{
    public function __construct(
        public int $capacity,
        public int $dayOfWeekId,
        public int $timeSlotId,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'capacity' => $this->capacity,
            'dayOfWeekId' => $this->dayOfWeekId,
            'timeSlotId' => $this->timeSlotId,
        ];
    }

    /**
     * @param array<string, int> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            capacity: $data['capacity'],
            dayOfWeekId: $data['dayOfWeekId'],
            timeSlotId: $data['timeSlotId'] ?? $data['turnId'] ?? 0,
        );
    }
}
