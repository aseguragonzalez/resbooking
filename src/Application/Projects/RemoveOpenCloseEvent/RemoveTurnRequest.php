<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveOpenCloseEvent;

use App\Domain\Shared\Turn;
use Seedwork\Application\UseCaseRequest;

final class RemoveOpenCloseEventRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly \DateTimeImmutable $date,
        public readonly Turn $turn,
    ) {
    }
}
