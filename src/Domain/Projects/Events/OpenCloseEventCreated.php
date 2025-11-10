<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class OpenCloseEventCreated extends DomainEvent
{
    public static function new(string $projectId, OpenCloseEvent $openCloseEvent): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'OpenCloseEventCreated',
            payload: ['projectId' => $projectId, 'openCloseEvent' => $openCloseEvent]
        );
    }

    public static function build(string $projectId, OpenCloseEvent $openCloseEvent, string $id): self
    {
        return new self(
            id: $id,
            type: 'OpenCloseEventCreated',
            payload: ['projectId' => $projectId, 'openCloseEvent' => $openCloseEvent]
        );
    }
}
