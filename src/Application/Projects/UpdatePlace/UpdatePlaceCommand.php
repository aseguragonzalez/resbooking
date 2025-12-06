<?php

declare(strict_types=1);

namespace Application\Projects\UpdatePlace;

final class UpdatePlaceCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $placeId,
        public readonly string $name,
        public readonly int $capacity
    ) {
    }
}
