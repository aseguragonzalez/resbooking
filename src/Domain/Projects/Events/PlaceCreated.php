<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\Place;
use App\Seedwork\Domain\DomainEvent;

final class PlaceCreated extends DomainEvent
{
    public static function new(string $projectId, Place $place): self
    {
        return new self(
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
