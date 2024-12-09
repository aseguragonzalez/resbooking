<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Authentication
{
    public function __construct(
        public int $idProject,
        public int $idRole,
        public int $idService,
        public int $idUser,
        public string $username,
        public string $password,
        public string $role,
        public string $service,
    ) { }
}
