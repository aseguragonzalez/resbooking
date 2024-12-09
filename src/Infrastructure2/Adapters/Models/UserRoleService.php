<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class UserRoleService
{
    public function __construct(
        public int $id,
        public int $idUser,
        public int $idService,
        public int $idRole,
    ) { }
}
