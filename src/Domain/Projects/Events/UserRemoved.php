<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\ValueObjects\User;
use Seedwork\Domain\DomainEvent;

final class UserRemoved extends DomainEvent
{
    public static function new(string $projectId, User $user): self
    {
        return new self(
            id: uniqid(),
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
