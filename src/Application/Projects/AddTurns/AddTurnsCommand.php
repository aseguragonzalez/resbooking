<?php

declare(strict_types=1);

namespace Application\Projects\AddTurns;

use Application\Projects\AddTurns\TurnItem;

final class AddTurnsCommand
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
