<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Routes;

final class DuplicatedRouteException extends \Exception
{
    public function __construct(Route $route)
    {
        parent::__construct("Route already registered: {$route}");
    }
}
