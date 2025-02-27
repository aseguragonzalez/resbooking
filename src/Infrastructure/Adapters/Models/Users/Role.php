<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Users;

final readonly class Role
{
    public function __construct(public int $id, public string $name)
    {
    }
}
