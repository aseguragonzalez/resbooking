<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\User;
use App\Seedwork\Domain\DomainEvent;

final class UserCreated extends DomainEvent
{
    public static function new(string $projectId, User $user): self
    {
        return new self(
            type: 'UserCreated',
            payload: ['projectId' => $projectId, 'user' => $user]
        );
    }

    public static function build(string $projectId, User $user, string $id): self
    {
        return new self(
            id: $id,
            type: 'UserCreated',
            payload: ['projectId' => $projectId, 'user' => $user]
        );
    }
}
