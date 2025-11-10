<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

use Seedwork\Application\Command;

final class AddOpencloseEventCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly \DateTimeImmutable $date,
        public readonly bool $isAvailable,
        public readonly string $startTime
    ) {
    }
}
