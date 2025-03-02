<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

abstract class Controller
{
    protected function view(
        ?string $name = null,
        ?object $model = null,
        StatusCode $statusCode = StatusCode::Ok,
    ): Response {
        $backtrace = debug_backtrace();
        if (!isset($backtrace[1]['class'])) {
            throw new \Exception('Class not found in backtrace');
        }

        $viewName = $name ? $name : $backtrace[1]['function'];
        $viewPath = str_replace("Controller", "", basename(str_replace('\\', '/', $backtrace[1]['class'])));
        return new ViewResponse(name: "{$viewPath}/{$viewName}", data: $model, statusCode: $statusCode);
    }

    protected function json(?object $model = null, StatusCode $statusCode = StatusCode::Ok): Response
    {
        throw new \Exception('Not implemented');
    }
}
