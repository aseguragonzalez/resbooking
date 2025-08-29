<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware
{
    protected function __construct(protected ?Middleware $next)
    {
    }

    public function setNext(Middleware $next): void
    {
        $this->next = $next;
    }

    abstract public function handleRequest(ServerRequestInterface $request): ResponseInterface;
}
