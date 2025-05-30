<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\Place;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class PlaceRemoved extends DomainEvent
{
    public static function new(string $projectId, Place $place): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'PlaceRemoved',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }

    public static function build(string $projectId, Place $place, string $id): self
    {
        return new self(
            id: $id,
            type: 'PlaceRemoved',
            payload: ['projectId' => $projectId, 'place' => $place]
        );
    }
}
