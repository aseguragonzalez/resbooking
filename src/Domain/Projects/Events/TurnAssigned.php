<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Seedwork\Domain\DomainEvent;

final class TurnAssigned extends DomainEvent
{
    public static function new(string $projectId, TurnAvailability $turn): self
    {
        return new self(
            type: 'TurnAssigned',
            payload: ['projectId' => $projectId, 'turn' => $turn]
        );
    }

    public static function build(string $projectId, TurnAvailability $turn, string $id): self
    {
        return new self(
            id: $id,
            type: 'TurnAssigned',
            payload: ['projectId' => $projectId, 'turn' => $turn]
        );
    }
}
