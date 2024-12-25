<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class ServiceRole
{
    public function __construct(
        public int $id,
        public int $serviceId,
        public int $roleId,
    ) {
    }
}
