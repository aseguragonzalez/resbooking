<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use App\Seedwork\Domain\Exceptions\InvalidOperationException;

final class UserAlreadyExistsException extends InvalidOperationException
{
    public function __construct()
    {
        parent::__construct('User already exists in project');
    }
}
