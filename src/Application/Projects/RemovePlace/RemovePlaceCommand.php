<?php

declare(strict_types=1);

namespace App\Application\Projects\RemovePlace;

use Seedwork\Application\Command;

final class RemovePlaceCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $placeId
    ) {
    }
}
