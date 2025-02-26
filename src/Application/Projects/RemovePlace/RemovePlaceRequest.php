<?php

declare(strict_types=1);

namespace App\Application\Projects\RemovePlace;

use App\Seedwork\Application\UseCaseRequest;

final class RemovePlaceRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $placeId
    ) {
    }
}
