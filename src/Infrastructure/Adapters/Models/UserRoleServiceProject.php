<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class UserRoleServiceProject
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $serviceId,
        public int $roleId,
        public int $projectId,
    ) {
    }
}
