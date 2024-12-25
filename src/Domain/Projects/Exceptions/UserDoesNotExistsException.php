<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use App\Seedwork\Domain\Exceptions\InvalidOperationException;

final class UserDoesNotExistsException extends InvalidOperationException
{
    public function __construct()
    {
        parent::__construct('User does not exists in project');
    }
}
