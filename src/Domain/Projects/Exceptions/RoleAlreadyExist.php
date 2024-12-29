<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class RoleAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Role already exist in project');
    }
}
