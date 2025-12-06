<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;

final class UpdateSettingsCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly Email $email,
        public readonly bool $hasReminders,
        public readonly string $name,
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly Capacity $numberOfTables,
        public readonly Phone $phone,
    ) {
    }
}
