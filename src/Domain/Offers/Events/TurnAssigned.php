<?php

declare(strict_types=1);

namespace Domain\Offers\Events;

use Domain\Shared\ValueObjects\TurnAvailability;
use Seedwork\Domain\DomainEvent;

final class TurnAssigned extends DomainEvent
{
    public static function new(string $offerId, TurnAvailability $turn): self
    {
        return new self(
            id: uniqid(),
            type: 'TurnAssigned',
            payload: ['offerId' => $offerId, 'turn' => $turn]
        );
    }

    public static function build(string $offerId, TurnAvailability $turn, string $id): self
    {
        return new self(
            id: $id,
            type: 'TurnAssigned',
            payload: ['offerId' => $offerId, 'turn' => $turn]
        );
    }
}
