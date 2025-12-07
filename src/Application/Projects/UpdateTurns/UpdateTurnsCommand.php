<?php

declare(strict_types=1);

namespace Application\Projects\UpdateTurns;

final class UpdateTurnsCommand
{
    /**
     * @param array<TurnAvailability> $turns
     */
    public function __construct(
        public readonly string $projectId,
        public readonly array $turns,
    ) {
    }
}
