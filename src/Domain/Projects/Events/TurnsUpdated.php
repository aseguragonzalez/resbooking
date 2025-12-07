<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\ValueObjects\TurnAvailability;
use Seedwork\Domain\DomainEvent;

final readonly class TurnsUpdated extends DomainEvent
{
    /**
     * @param array<TurnAvailability> $turns
     */
    public static function new(string $projectId, array $turns): self
    {
        return new self(
            id: uniqid(),
            type: 'TurnsUpdated',
            payload: ['projectId' => $projectId, 'turns' => $turns]
        );
    }
}
