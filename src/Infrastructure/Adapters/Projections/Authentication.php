<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Authentication
{
    public function __construct(
        public int $projectId,
        public int $roleId,
        public int $serviceId,
        public int $userId,
        public string $username,
        public string $password,
        public string $role,
        public string $service,
    ) {
    }
}
