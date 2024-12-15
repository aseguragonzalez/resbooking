<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Seedwork\Domain\Entity;

final class User extends Entity
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly bool $blocked = false,
        public readonly array $roles = [],
    ) { }
}
