<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Domain\Exceptions;

final class UserBlockedException extends \Exception
{
    public function __construct(string $username = "")
    {
        parent::__construct("User is blocked: {$username}.");
    }
}
