<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

use Seedwork\Domain\EntityId;

final readonly class UpdateSettingsCommand
{
    public function __construct(
        public EntityId $restaurantId,
        public string $email,
        public bool $hasReminders,
        public string $name,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public int $numberOfTables,
        public string $phone,
    ) {
    }
}
