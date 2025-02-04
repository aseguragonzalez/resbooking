<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\ValueObjects\User;
use App\Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class UserRemoved extends DomainEvent
{
    public static function new(string $projectId, User $user): self
    {
        return new self(
            id: (string)new Ksuid(),
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
