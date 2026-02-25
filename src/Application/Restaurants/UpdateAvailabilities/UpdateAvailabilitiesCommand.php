<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

use SeedWork\Application\Command;

final readonly class UpdateAvailabilitiesCommand extends Command
{
    /**
     * @param array<int, array{dayOfWeekId: int, timeSlotId: int, capacity: int}> $availabilities
     */
    public function __construct(
        public string $restaurantId,
        public array $availabilities,
    ) {
        parent::__construct();
    }
}
