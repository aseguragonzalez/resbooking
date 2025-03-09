<?php

declare(strict_types=1);

namespace App\Application\Users\EnableUser;

use Seedwork\Application\UseCaseRequest;

final class EnableUserRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username)
    {
    }
}
