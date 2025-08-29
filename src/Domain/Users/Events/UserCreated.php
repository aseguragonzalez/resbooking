<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Shared\{Password, Role};
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class UserCreated extends DomainEvent
{
    /**
     * @param array<Role> $roles An array representing the roles assigned to the user.
     */
    public static function new(string $username, array $roles, ?Password $password): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'UserCreated',
            payload: [
                'username' => $username,
                'roles' => $roles,
                'password' => $password
            ]
        );
    }

    /**
     * @param array<Role> $roles An array representing the roles assigned to the user.
     */
    public static function build(string $username, array $roles, string $id, ?Password $password): self
    {
        return new self(
            id: $id,
            type: 'UserCreated',
            payload: [
                'username' => $username,
                'roles' => $roles,
                'password' => $password
            ]
        );
    }
}
