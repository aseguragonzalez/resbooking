<?php

declare(strict_types=1);

namespace App\Domain\Offers\Events;

use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use App\Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class OpenCloseEventRemoved extends DomainEvent
{
    public static function new(string $offerId, OpenCloseEvent $openCloseEvent): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'OpenCloseEventRemoved',
            payload: ['offerId' => $offerId, 'openCloseEvent' => $openCloseEvent]
        );
    }

    public static function build(string $offerId, OpenCloseEvent $openCloseEvent, string $id): self
    {
        return new self(
            id: $id,
            type: 'OpenCloseEventRemoved',
            payload: ['offerId' => $offerId, 'openCloseEvent' => $openCloseEvent]
        );
    }
}
