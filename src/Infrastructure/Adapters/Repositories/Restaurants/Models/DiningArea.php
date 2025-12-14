<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants\Models;

final readonly class DiningArea implements \JsonSerializable
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

    /**
     * @param array<string, string|int> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        /** @var string $id */
        $id = $data['id'] ?? '';
        /** @var int $capacity */
        $capacity = $data['capacity'] ?? 0;
        /** @var string $name */
        $name = $data['name'] ?? '';
        return new self(
            id: $id,
            capacity: $capacity,
            name: $name,
        );
    }
}
