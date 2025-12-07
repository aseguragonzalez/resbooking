<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\ValueObjects\OpenCloseEvent;
use Seedwork\Domain\DomainEvent;

final readonly class OpenCloseEventRemoved extends DomainEvent
{
    public static function new(string $projectId, OpenCloseEvent $openCloseEvent): self
    {
        return new self(
            id: uniqid(),
            type: 'OpenCloseEventRemoved',
            payload: ['projectId' => $projectId, 'openCloseEvent' => $openCloseEvent]
        );
    }
}
