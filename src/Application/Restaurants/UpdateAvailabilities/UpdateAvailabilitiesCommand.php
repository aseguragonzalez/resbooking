<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

use Seedwork\Domain\EntityId;

final readonly class UpdateAvailabilitiesCommand
{
    /**
     * @param array<Availability> $availabilities
     */
    public function __construct(
        public EntityId $restaurantId,
        public array $availabilities,
    ) {
    }
}
