<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

abstract class Controller
{
    abstract protected function view(
        ?string $name = null,
        ?object $model = null,
        StatusCode $statusCode = StatusCode::Ok,
    ): Response;

    abstract protected function json(?object $model = null, StatusCode $statusCode = StatusCode::Ok): Response;
}
