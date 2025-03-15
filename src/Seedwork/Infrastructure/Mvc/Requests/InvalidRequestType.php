<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

final class InvalidRequestType extends \Exception
{
    public function __construct(string $requestType)
    {
        parent::__construct("Class $requestType does not exist.");
    }
}
