<?php

declare(strict_types=1);

namespace App\Seedwork\Infrastructure\Mvc\Routes;

final class RouteDoesNotFoundException extends \Exception
{
    public function __construct(string $method, string $path)
    {
        parent::__construct("Route not found: $method $path");
    }
}
