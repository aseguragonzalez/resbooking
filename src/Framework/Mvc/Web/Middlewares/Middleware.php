<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware
{
    protected function __construct(public ?Middleware $next)
    {
    }

    public function setNext(Middleware $next): void
    {
        $this->next = $next;
    }

    abstract public function handleRequest(ServerRequestInterface $request): ResponseInterface;
}
