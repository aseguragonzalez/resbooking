<?php

declare(strict_types=1);

namespace App\Domain\Offers\Events;

use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class OpenCloseEventCreated extends DomainEvent
{
    public static function new(string $offerId, OpenCloseEvent $openCloseEvent): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'OpenCloseEventCreated',
            payload: ['offerId' => $offerId, 'openCloseEvent' => $openCloseEvent]
        );
    }

    public static function build(string $offerId, OpenCloseEvent $openCloseEvent, string $id): self
    {
        return new self(
            id: $id,
            type: 'OpenCloseEventCreated',
            payload: ['offerId' => $offerId, 'openCloseEvent' => $openCloseEvent]
        );
    }
}
