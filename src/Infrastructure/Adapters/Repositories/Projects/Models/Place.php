<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class Place implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public int $capacity,
        public string $name,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'capacity' => $this->capacity,
            'name' => $this->name,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            capacity: $data['capacity'],
            name: $data['name'],
        );
    }
}
