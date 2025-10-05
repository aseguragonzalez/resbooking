<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Seedwork\Infrastructure\Mvc\Settings;

final class Authentication extends Middleware
{
    public function __construct(
        private readonly IdentityManager $identityManager,
        private readonly Settings $settings,
        private readonly ResponseFactoryInterface $responseFactory,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }
        $cookies = $request->getCookieParams();
        /** @var string $token */
        $token = $cookies[$this->settings->authCookieName] ?? '';
        try {
            $identity = $this->identityManager->getIdentity($token);
            /** @var RequestContext $context */
            $context = $request->getAttribute(RequestContext::class);
            $context->setIdentity($identity);
            $context->setIdentityToken($token);
            return $this->next->handleRequest($request);
        } catch (SessionExpiredException) {
            $cookie = "{$this->settings->authCookieName}=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0";
            return $this->responseFactory
                ->createResponse(303)
                ->withHeader('Location', $this->settings->authLoginUrl)
                ->withHeader('Set-Cookie', $cookie);
        }
    }
}
