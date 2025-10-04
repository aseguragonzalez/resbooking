<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions;

final class InvalidCredentialsException extends \Exception
{
    public function __construct(string $username = "")
    {
        parent::__construct("Invalid credentials for user: {$username} or user does not exist.");
    }
}
