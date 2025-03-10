<?php

declare(strict_types=1);

namespace App\Application\Projects\AddPlace;

use Seedwork\Application\UseCaseRequest;

final class AddPlaceRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $name,
        public readonly int $capacity
    ) {
    }
}
