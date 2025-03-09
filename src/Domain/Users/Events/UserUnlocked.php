<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Users\Entities\User;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class UserUnlocked extends DomainEvent
{
    public static function new(string $username, User $user): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'UserUnlocked',
            payload: ['username' => $username, 'user' => $user]
        );
    }

    public static function build(string $username, User $user, string $id): self
    {
        return new self(
            id: $id,
            type: 'UserUnlocked',
            payload: ['username' => $username, 'user' => $user]
        );
    }
}
