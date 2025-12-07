<?php

declare(strict_types=1);

namespace Application\Projects\RemovePlace;

final readonly class RemovePlaceCommand
{
    public function __construct(
        public string $projectId,
        public string $placeId
    ) {
    }
}
