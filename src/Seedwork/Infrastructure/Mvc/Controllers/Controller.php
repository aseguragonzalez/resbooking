<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Controllers;

use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\{RedirectTo, Response, StatusCode};
use Seedwork\Infrastructure\Mvc\Views\View;

abstract class Controller
{
    /**
     * @var array<Header> $headers
     */
    private array $headers = [];

    protected function addHeader(Header $header): void
    {
        $this->headers[] = $header;
    }

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
        return new View(
            data: $model,
            headers: array_merge($this->headers, []),
            statusCode: $statusCode,
            viewPath: "{$viewPath}/{$viewName}"
        );
    }

    protected function redirectTo(string $url, ?object $args = null): Response
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided');
        }
        return new RedirectTo($url, $args ?? new \stdClass(), headers: array_merge($this->headers, []));
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

        return new LocalRedirectTo(
            action: $action,
            args: $args ?? new \stdClass(),
            headers: array_merge($this->headers, []),
            controller: $controller ?? basename(str_replace('\\', '/', $backtrace[1]['class'])),
        );
    }
}
