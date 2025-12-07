<?php

declare(strict_types=1);

namespace Application\Projects\RemoveOpenCloseEvent;

use Domain\Shared\Turn;

final readonly class RemoveOpenCloseEventCommand
{
    public function __construct(
        public string $projectId,
        public \DateTimeImmutable $date,
        public Turn $turn,
    ) {
    }
}
