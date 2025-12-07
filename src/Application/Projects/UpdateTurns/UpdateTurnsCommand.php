<?php

declare(strict_types=1);

namespace Application\Projects\UpdateTurns;

final readonly class UpdateTurnsCommand
{
    /**
     * @param array<TurnAvailability> $turns
     */
    public function __construct(
        public string $projectId,
        public array $turns,
    ) {
    }
}
