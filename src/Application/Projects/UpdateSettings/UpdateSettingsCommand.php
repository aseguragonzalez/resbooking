<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateSettings;

use App\Domain\Shared\{Capacity, Email, Phone};
use Seedwork\Application\Command;

final class UpdateSettingsCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly Email $email,
        public readonly bool $hasRemainders,
        public readonly string $name,
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly Capacity $numberOfTables,
        public readonly Phone $phone,
    ) {
    }
}
