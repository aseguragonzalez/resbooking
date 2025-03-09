<?php

declare(strict_types=1);

namespace App\Domain\Users\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class UserAlreadyUnlocked extends DomainException
{
    public function __construct()
    {
        parent::__construct('User is already unlocked.');
    }
}
