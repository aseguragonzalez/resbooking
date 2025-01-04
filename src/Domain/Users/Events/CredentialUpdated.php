<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Shared\Password;
use App\Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class CredentialUpdated extends DomainEvent
{
    public static function new(string $username, Password $password): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'CredentialUpdated',
            payload: ['username' => $username, 'password' => $password]
        );
    }

    public static function build(string $username, Password $password, string $id): self
    {
        return new self(
            id: $id,
            type: 'CredentialUpdated',
            payload: ['username' => $username, 'password' => $password]
        );
    }
}
