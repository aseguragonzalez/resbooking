<?php

declare(strict_types=1);

namespace Domain\Projects\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class UserAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('User already exists in project');
    }
}
