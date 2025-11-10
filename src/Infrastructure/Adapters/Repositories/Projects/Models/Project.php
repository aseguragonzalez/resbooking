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

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        /** @var string $id */
        $id = $data['id'] ?? '';
        /** @var array<string, mixed> $settings */
        $settings = (array) $data['settings'];
        /** @var array<string, array<string, int|string>> $places */
        $places = (array) $data['places'];
        /** @var array<string, array<string, int>> $turns */
        $turns = (array) $data['turnAvailabilities'];
        /** @var array<string> $users */
        $users = $data['users'];
        return new self(
            id: $id,
            settings: Settings::fromArray($settings),
            places: array_map(fn ($placeData) => Place::fromArray((array) $placeData), $places),
            turnAvailabilities: array_map(fn ($turnData) => TurnAvailability::fromArray((array) $turnData), $turns),
            users: $users,
        );
    }
}
