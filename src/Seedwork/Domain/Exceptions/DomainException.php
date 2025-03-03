<?php

declare(strict_types=1);

namespace App\Seedwork\Domain\Exceptions;

abstract class DomainException extends \Exception
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
