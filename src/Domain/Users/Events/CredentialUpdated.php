<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class CredentialUpdated extends DomainEvent
{
    public static function new(string $username): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'CredentialUpdated',
            payload: ['username' => $username]
        );
    }

    public static function build(string $username, string $id): self
    {
        return new self(
            id: $id,
            type: 'CredentialUpdated',
            payload: ['username' => $username]
        );
    }
}
