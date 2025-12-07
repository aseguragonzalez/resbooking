<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\Entities\Place;
use Seedwork\Domain\DomainEvent;

final readonly class PlaceRemoved extends DomainEvent
{
    public static function new(string $projectId, Place $place): self
    {
        return new self(
            id: uniqid(),
            type: 'PlaceRemoved',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }
}
