<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Controllers;

use Seedwork\Infrastructure\Mvc\Actions\Responses\{ActionResponse, LocalRedirectTo, RedirectTo, View};
use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

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
    ): ActionResponse {
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

    protected function redirectTo(string $url, ?object $args = null): ActionResponse
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
    ): ActionResponse {
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
