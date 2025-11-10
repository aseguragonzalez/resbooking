<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\Entities\Place;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class PlaceCreated extends DomainEvent
{
    public static function new(string $projectId, Place $place): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'PlaceCreated',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }

    public static function build(string $projectId, Place $place, string $id): self
    {
        return new self(
            id: $id,
            type: 'PlaceCreated',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }
}
