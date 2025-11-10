<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

use Seedwork\Application\Command;

final class AddPlaceCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $name,
        public readonly int $capacity
    ) {
    }
}
