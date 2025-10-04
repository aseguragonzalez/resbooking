<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions;

final class UserIsNotActiveException extends \Exception
{
    public function __construct(string $username = "")
    {
        parent::__construct("User is not active: {$username}.");
    }
}
