<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

abstract class Controller
{
    /**
     * @param array<string, string> $headers
     */
    abstract protected function view(
        array $headers = [],
        ?string $name = null,
        ?object $model = null,
        StatusCode $statusCode = StatusCode::Ok,
    ): Response;

    /**
     * @param array<string, string> $headers
     */
    abstract protected function json(
        array $headers = [],
        ?string $name = null,
        ?object $model = null,
        StatusCode $statusCode = StatusCode::Ok,
    ): Response;
}
