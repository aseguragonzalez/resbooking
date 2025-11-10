<?php

declare(strict_types=1);

namespace Application\Projects\RemoveTurn;

use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
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
