<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions;

final class InvalidObjectType extends \Exception
{
    public function __construct(string $objectType)
    {
        parent::__construct("Class $objectType does not exist.");
    }
}
