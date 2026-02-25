<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

use SeedWork\Application\Command;

final readonly class UpdateSettingsCommand extends Command
{
    public function __construct(
        public string $restaurantId,
        public string $email,
        public bool $hasReminders,
        public string $name,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public int $numberOfTables,
        public string $phone,
    ) {
        parent::__construct();
    }
}
