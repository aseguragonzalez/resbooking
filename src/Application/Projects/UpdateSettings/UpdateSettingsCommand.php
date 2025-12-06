<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

final class UpdateSettingsCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $email,
        public readonly bool $hasReminders,
        public readonly string $name,
        public readonly int $maxNumberOfDiners,
        public readonly int $minNumberOfDiners,
        public readonly int $numberOfTables,
        public readonly string $phone,
    ) {
    }
}
