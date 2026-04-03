<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Domain\Exceptions;

final class SessionExpiredException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Session has expired.");
    }
}
