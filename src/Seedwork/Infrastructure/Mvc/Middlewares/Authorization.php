<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

final class Authorization extends Middleware
{
    public function __construct(private readonly Router $router, ?Middleware $next = null)
    {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        $path = $request->getUri()->getPath();
        $method = RouteMethod::fromString($request->getMethod());
        $route = $this->router->get($method, $path);
        /** @var RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        $identity = $context->getIdentity();
        $route->ensureAuthenticated($identity);
        $route->ensureAuthorized($identity);
        return $this->next->handleRequest($request);
    }
}
