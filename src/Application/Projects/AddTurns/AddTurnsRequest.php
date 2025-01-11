<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Application\Projects\AddTurns\TurnItem;
use App\Seedwork\Application\UseCaseRequest;

final class AddTurnsRequest extends UseCaseRequest
{
    /**
     * @param string $projectId
     * @param array<TurnItem> $turns
     */
    public function __construct(
        public readonly string $projectId,
        public readonly array $turns,
    ) {
    }
}
