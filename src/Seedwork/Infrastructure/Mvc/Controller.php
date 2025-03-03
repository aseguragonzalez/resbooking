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
        return new ViewResponse(viewPath: "{$viewPath}/{$viewName}", data: $model, statusCode: $statusCode);
    }

    protected function redirectTo(string $url, ?object $args = null): Response
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided');
        }
        return new RedirectToResponse($url, $args ?? new \stdClass());
    }

    protected function redirectToAction(
        string $action,
        ?string $controller = null,
        ?object $args = null,
    ): Response {
        $backtrace = debug_backtrace();
        if (!isset($backtrace[1]['class'])) {
            throw new \Exception('Class not found in backtrace');
        }

        return new LocalRedirectToResponse(
            controller: $controller ?? basename(str_replace('\\', '/', $backtrace[1]['class'])),
            action: $action,
            args: $args ?? new \stdClass()
        );
    }
}
