<?php

declare(strict_types=1);

namespace App\Application\Users\DisableUser;

use Seedwork\Application\UseCaseRequest;

final class DisableUserRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username)
    {
    }
}
