<?php

declare(strict_types=1);

namespace Application\Projects\RemoveOpenCloseEvent;

use Domain\Shared\Turn;
use Seedwork\Application\Command;

final class RemoveOpenCloseEventCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly \DateTimeImmutable $date,
        public readonly Turn $turn,
    ) {
    }
}
