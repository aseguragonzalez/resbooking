<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\ValueObjects\User;
use Seedwork\Domain\DomainEvent;

final readonly class UserRemoved extends DomainEvent
{
    public static function new(string $projectId, User $user): self
    {
        return new self(
            id: uniqid(),
            type: 'UserRemoved',
            payload: ['projectId' => $projectId, 'user' => $user]
        );
    }
}
