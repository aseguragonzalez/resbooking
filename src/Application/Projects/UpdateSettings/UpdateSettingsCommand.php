<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

final readonly class UpdateSettingsCommand
{
    public function __construct(
        public string $projectId,
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
