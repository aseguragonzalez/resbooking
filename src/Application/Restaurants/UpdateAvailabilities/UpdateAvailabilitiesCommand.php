<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

final readonly class UpdateAvailabilitiesCommand
{
    /**
     * @param array<Availability> $availabilities
     */
    public function __construct(
        public string $restaurantId,
        public array $availabilities,
    ) {
    }
}
