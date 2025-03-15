<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions;

final class InvalidDocComment extends \Exception
{
    public function __construct(string $paramName)
    {
        parent::__construct("Doc comment not found for parameter $paramName");
    }
}
