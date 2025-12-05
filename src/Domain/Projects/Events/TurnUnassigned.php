<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Shared\ValueObjects\TurnAvailability;
use Seedwork\Domain\DomainEvent;

final class TurnUnassigned extends DomainEvent
{
    public static function new(string $projectId, TurnAvailability $turn): self
    {
        return new self(
            id: uniqid(),
            type: 'TurnUnassigned',
            payload: ['projectId' => $projectId, 'turn' => $turn]
        );
    }

    public static function build(string $projectId, TurnAvailability $turn, string $id): self
    {
        return new self(
            id: $id,
            type: 'TurnUnassigned',
            payload: ['projectId' => $projectId, 'turn' => $turn]
        );
    }
}
