<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Users;

final readonly class UserRole
{
    public function __construct(public int $userId, public int $roleId)
    {
    }
}
