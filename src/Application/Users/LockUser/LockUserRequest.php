<?php

declare(strict_types=1);

namespace App\Application\Users\LockUser;

use Seedwork\Application\UseCaseRequest;

final class LockUserRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username)
    {
    }
}
