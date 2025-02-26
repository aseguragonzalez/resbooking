<?php

declare(strict_types=1);

namespace App\Application\Users\UnlockUser;

use App\Seedwork\Application\UseCaseRequest;

final class UnlockUserRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username)
    {
    }
}
