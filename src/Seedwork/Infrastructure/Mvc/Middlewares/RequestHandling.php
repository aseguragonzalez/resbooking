<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;

final class RequestHandling extends Middleware
{
    public function __construct(private readonly RequestHandler $requestHandler)
    {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->requestHandler->handle($request);
    }
}
