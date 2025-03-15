<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions;

final class InvalidParamName extends \Exception
{
    public function __construct(string $paramName)
    {
        parent::__construct("Parameter $paramName does not exist.");
    }
}
