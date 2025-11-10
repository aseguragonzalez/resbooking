<?php

declare(strict_types=1);

namespace Domain\Projects\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class UserDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('User does not exists in project');
    }
}
