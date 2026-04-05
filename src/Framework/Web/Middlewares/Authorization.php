<?php

declare(strict_types=1);

namespace Framework\Web\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Web\AuthSettings;
use Framework\Web\Requests\RequestContext;
use Framework\Web\Responses\Headers\Location;
use Framework\Web\Responses\Headers\SetCookie;
use Framework\Web\Responses\StatusCode;
use Framework\Web\Routes\AuthenticationRequiredException;
use Framework\Web\Routes\RouteMethod;
use Framework\Web\Routes\Router;

final class Authorization extends Middleware
{
    public function __construct(
        private readonly AuthSettings $settings,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Router $router,
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
            $setCookieHeader = SetCookie::removeCookie($this->settings->cookieName);
            $locationHeader = Location::toInternalUrl($this->settings->signInPath);
            return $this->responseFactory
                ->createResponse(StatusCode::SeeOther->value)
                ->withHeader($locationHeader->name, $locationHeader->value)
                ->withHeader($setCookieHeader->name, $setCookieHeader->value);
        }

        $route->ensureAuthorized($identity);

        return $this->next->handleRequest($request);
    }
}
