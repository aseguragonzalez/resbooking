<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

final class AddPlaceCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $name,
        public readonly int $capacity
    ) {
    }
}
