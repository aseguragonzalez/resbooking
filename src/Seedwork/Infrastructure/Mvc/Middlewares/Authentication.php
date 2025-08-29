<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Application\Logging\Logger;

final class Authentication extends Middleware
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }
        $this->logger->debug('Authentication middleware: request authenticated');
        return $this->next->handleRequest($request);
    }
}
