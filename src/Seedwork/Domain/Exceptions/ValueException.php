<?php

declare(strict_types=1);

namespace App\Seedwork\Domain\Exceptions;

class ValueException extends DomainException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
