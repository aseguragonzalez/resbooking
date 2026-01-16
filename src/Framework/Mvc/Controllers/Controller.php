<?php

declare(strict_types=1);

namespace Framework\Mvc\Controllers;

use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Actions\Responses\LocalRedirectTo;
use Framework\Mvc\Actions\Responses\RedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Responses\Headers\Header;
use Framework\Mvc\Responses\StatusCode;

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
        $viewName = $name ? $name : $backtrace[1]['function'];
        // @phpstan-ignore-next-line
        $viewPath = str_replace("Controller", "", basename(str_replace('\\', '/', $backtrace[1]['class'])));
        return new View("{$viewPath}/{$viewName}", $model, array_merge($this->headers, []), $statusCode);
    }

    /**
     * @param array<string, mixed>|null $args
     */
    protected function redirectTo(string $url, ?array $args = []): ActionResponse
    {
        return RedirectTo::create(url: $url, args: $args, headers: array_merge($this->headers, []));
    }

    /**
     * @param class-string $controller
     */
    protected function redirectToAction(
        string $action,
        ?string $controller = null,
        ?object $args = null,
    ): ActionResponse {
        $backtrace = debug_backtrace();
        // @phpstan-ignore-next-line
        $requestedController = $controller ?? $backtrace[1]['class'];
        return LocalRedirectTo::create($action, $requestedController, $args, array_merge($this->headers, []));
    }
}
