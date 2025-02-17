<?php

declare(strict_types=1);

namespace App\Application\Projects\AddUser;

use App\Seedwork\Application\UseCaseRequest;

final class AddUserRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $username,
        public readonly bool $isAdmin
    ) {
    }
}
