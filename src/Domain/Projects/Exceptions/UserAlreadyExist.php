<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class UserAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('User already exists in project');
    }
}
