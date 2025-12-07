<?php

declare(strict_types=1);

namespace Application\Projects\UpdatePlace;

final readonly class UpdatePlaceCommand
{
    public function __construct(
        public string $projectId,
        public string $placeId,
        public string $name,
        public int $capacity
    ) {
    }
}
