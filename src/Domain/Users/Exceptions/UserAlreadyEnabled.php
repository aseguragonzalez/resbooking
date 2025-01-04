<?php

declare(strict_types=1);

namespace App\Domain\Users\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class UserAlreadyEnabled extends DomainException
{
    public function __construct()
    {
        parent::__construct('User is already enabled.');
    }
}
