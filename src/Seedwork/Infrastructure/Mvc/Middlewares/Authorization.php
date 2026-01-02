<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Location;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Routes\AuthenticationRequiredException;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Settings;

final class Authorization extends Middleware
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Router $router,
        private readonly Settings $settings,
        ?Middleware $next = null
    ) {
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
        try {
            $route->ensureAuthenticated($identity);
        } catch (AuthenticationRequiredException) {
            $setCookieHeader = SetCookie::removeCookie($this->settings->authCookieName);
            $locationHeader = Location::redirectToInternal($this->settings->authLoginUrl);
            return $this->responseFactory
                ->createResponse(StatusCode::SeeOther->value)
                ->withHeader($locationHeader->name, $locationHeader->value)
                ->withHeader($setCookieHeader->name, $setCookieHeader->value);
        }
        $route->ensureAuthorized($identity);
        return $this->next->handleRequest($request);
    }
}
