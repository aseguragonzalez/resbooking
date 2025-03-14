<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveUser;

use Seedwork\Application\UseCaseRequest;

final class RemoveUserRequest extends UseCaseRequest
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $username
    ) {
    }
}
