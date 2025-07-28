<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Routes;

final class InvalidController extends \Exception
{
    public function __construct(string $controller)
    {
        parent::__construct("Controller {$controller} is not a valid controller");
    }
}
