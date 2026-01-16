<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestHandling extends Middleware
{
    public function __construct(
        private readonly RequestHandlerInterface $requestHandler,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->requestHandler->handle($request);
    }
}
