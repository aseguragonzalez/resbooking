<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use App\Seedwork\Domain\DomainEvent;

final class OpenCloseEventCreated extends DomainEvent
{
    public static function new(string $projectId, OpenCloseEvent $openCloseEvent): self
    {
        return new self(
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
