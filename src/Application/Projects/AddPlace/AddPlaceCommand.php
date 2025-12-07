<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

final readonly class AddPlaceCommand
{
    public function __construct(
        public string $projectId,
        public string $name,
        public int $capacity
    ) {
    }
}
