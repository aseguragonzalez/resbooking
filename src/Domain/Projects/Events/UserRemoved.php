<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\User;
use App\Seedwork\Domain\DomainEvent;

final class UserRemoved extends DomainEvent
{
    public static function new(string $projectId, User $user): self
    {
        return new self(
            type: 'UserRemoved',
            payload: ['projectId' => $projectId, 'user' => $user]
        );
    }

    public static function build(string $projectId, User $user, string $id): self
    {
        return new self(
            id: $id,
            type: 'UserRemoved',
            payload: ['projectId' => $projectId, 'user' => $user]
        );
    }
}
