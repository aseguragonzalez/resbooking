<?php

declare(strict_types=1);

namespace App\Domain\Offers\Events;

use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class TurnAssigned extends DomainEvent
{
    public static function new(string $offerId, TurnAvailability $turn): self
    {
        return new self(
            id: (string)new Ksuid(),
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
