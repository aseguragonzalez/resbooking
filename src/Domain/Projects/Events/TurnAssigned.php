<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\ValueObjects\TurnAvailability;
use Seedwork\Domain\DomainEvent;

final class TurnAssigned extends DomainEvent
{
    public static function new(string $projectId, TurnAvailability $turn): self
    {
        return new self(
            id: uniqid(),
            type: 'TurnAssigned',
            payload: ['projectId' => $projectId, 'turn' => $turn]
        );
    }
}
