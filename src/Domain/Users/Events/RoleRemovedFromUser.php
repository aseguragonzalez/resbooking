<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Shared\Role;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class RoleRemovedFromUser extends DomainEvent
{
    public static function new(string $username, Role $role): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'RoleRemovedFromUser',
            payload: ['username' => $username, 'role' => $role]
        );
    }

    public static function build(string $username, Role $role, string $id): self
    {
        return new self(
            id: $id,
            type: 'RoleRemovedFromUser',
            payload: ['username' => $username, 'role' => $role]
        );
    }
}
