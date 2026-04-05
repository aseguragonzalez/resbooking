<?php

declare(strict_types=1);

namespace Framework\Controllers;

use Framework\Actions\Responses\ActionResponse;
use Framework\Actions\Responses\LocalRedirectTo;
use Framework\Actions\Responses\RedirectTo;
use Framework\Actions\Responses\View;
use Framework\Responses\Headers\Header;
use Framework\Responses\StatusCode;

abstract class Controller
{
    /**
     * @param array<Header> $headers
     */
    public function __construct(private array $headers = [])
    {
    }

    protected function addHeader(Header $header): void
    {
        $this->headers[] = $header;
    }

    protected function view(
        string $viewPath,
        ?object $model = null,
        StatusCode $statusCode = StatusCode::Ok,
    ): ActionResponse {
        return new View($viewPath, $model, array_merge($this->headers, []), $statusCode);
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
        string $controller,
        ?object $args = null,
    ): ActionResponse {
        return LocalRedirectTo::create($action, $controller, $args, array_merge($this->headers, []));
    }
}
