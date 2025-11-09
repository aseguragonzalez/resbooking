<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveTurn;

use App\Domain\Shared\{DayOfWeek, Turn};
use Seedwork\Application\Command;

final class RemoveTurnCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly DayOfWeek $dayOfWeek,
        public readonly Turn $turn,
    ) {
    }
}
