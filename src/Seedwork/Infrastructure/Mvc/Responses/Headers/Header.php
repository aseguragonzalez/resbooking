<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

abstract class Header
{
    public function __construct(public readonly string $name, public readonly string $value)
    {
    }
}
