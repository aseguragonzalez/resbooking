<?php

declare(strict_types=1);

namespace App\Application\Projects\AddOpenCloseEvent;

use App\Seedwork\Application\UseCaseRequest;

final class AddOpenCloseEventRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly \DateTimeImmutable $date,
        public readonly bool $isAvailable,
        public readonly string $startTime
    ) {
    }
}
