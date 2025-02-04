<?php

declare(strict_types=1);

namespace App\Seedwork\Exceptions;

use Exception;

class NotImplementedException extends Exception
{
    public function __construct(string $message = "Method not implemented")
    {
        parent::__construct($message);
    }
}
