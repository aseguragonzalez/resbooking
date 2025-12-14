<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants\Models;

final readonly class Restaurant implements \JsonSerializable
{
    /**
     * @param array<DiningArea> $diningAreas
     * @param array<Availability> $availabilities
     * @param array<string> $users
     */
    public function __construct(
        public string $id,
        public Settings $settings,
        public array $diningAreas = [],
        public array $availabilities = [],
        public array $users = [],
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'settings' => $this->settings,
            'diningAreas' => $this->diningAreas,
            'availabilities' => $this->availabilities,
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
        /** @var array<string, array<string, int|string>> $diningAreas */
        $diningAreas = (array) ($data['diningAreas'] ?? $data['places'] ?? []);
        /** @var array<string, array<string, int>> $availabilities */
        $availabilities = (array) ($data['availabilities'] ?? $data['turnAvailabilities'] ?? []);
        /** @var array<string> $users */
        $users = $data['users'] ?? [];
        return new self(
            id: $id,
            settings: Settings::fromArray($settings),
            diningAreas: array_map(
                fn ($diningAreaData) => DiningArea::fromArray((array) $diningAreaData),
                $diningAreas
            ),
            availabilities: array_map(
                fn ($availabilityData) => Availability::fromArray((array) $availabilityData),
                $availabilities
            ),
            users: $users,
        );
    }
}
