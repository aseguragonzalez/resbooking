<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\Entities\Place;
use Seedwork\Domain\DomainEvent;

final class PlaceCreated extends DomainEvent
{
    public static function new(string $projectId, Place $place): self
    {
        return new self(
            id: uniqid(),
            type: 'PlaceCreated',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }
}
