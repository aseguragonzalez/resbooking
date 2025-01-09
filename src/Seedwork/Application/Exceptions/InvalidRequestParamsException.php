<?php

declare(strict_types=1);

namespace App\Seedwork\Application\Exceptions;

final class InvalidRequestException extends Exception
{
    public function __construct()
    {
        parent::__construct('Unkown use case request');
    }
}
