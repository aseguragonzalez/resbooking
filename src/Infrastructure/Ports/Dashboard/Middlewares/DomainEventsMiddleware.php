<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Middlewares;

use Framework\Mvc\Middlewares\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Application\Messaging\DomainEventsBus;

final class DomainEventsMiddleware extends Middleware
{
    public function __construct(
        private readonly DomainEventsBus $domainEventsBus,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        $response = $this->next->handleRequest($request);
        $this->domainEventsBus->notify();

        return $response;
    }
}
