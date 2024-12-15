<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Role
{
    public readonly static Role $admin = new Role(1, 'admin');
    public readonly static Role $user = new Role(2, 'host');

    private function __construct(
        public readonly int $id,
        public readonly string $name,
    ) { }
}
