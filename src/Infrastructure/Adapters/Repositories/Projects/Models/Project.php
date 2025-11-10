<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class Project implements \JsonSerializable
{
    /**
     * @param array<Place> $places
     * @param array<TurnAvailability> $turnAvailabilities
     * @param array<string> $users
     */
    public function __construct(
        public string $id,
        public Settings $settings,
        public array $places = [],
        public array $turnAvailabilities = [],
        public array $users = [],
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'settings' => $this->settings,
            'places' => $this->places,
            'turnAvailabilities' => $this->turnAvailabilities,
            'users' => $this->users,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            settings: Settings::fromArray((array) $data['settings']),
            places: array_map(
                fn ($placeData) => Place::fromArray((array) $placeData),
                (array) $data['places'] ?? []
            ),
            turnAvailabilities: array_map(
                fn ($turnData) => TurnAvailability::fromArray((array) $turnData),
                (array) $data['turnAvailabilities'] ?? []
            ),
            users: $data['users'] ?? [],
        );
    }
}
