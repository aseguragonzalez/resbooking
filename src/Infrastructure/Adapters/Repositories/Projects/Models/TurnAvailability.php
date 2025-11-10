<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class TurnAvailability implements \JsonSerializable
{
    public function __construct(
        public int $capacity,
        public int $dayOfWeekId,
        public int $turnId,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'capacity' => $this->capacity,
            'dayOfWeekId' => $this->dayOfWeekId,
            'turnId' => $this->turnId,
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
            turnId: $data['turnId'],
        );
    }
}
