<?php

declare(strict_types=1);

namespace Application\Projects\RemovePlace;

final class RemovePlaceCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $placeId
    ) {
    }
}
