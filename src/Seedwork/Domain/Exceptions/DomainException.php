<?php

declare(strict_types=1);

namespace App\Seedwork\Domain\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
