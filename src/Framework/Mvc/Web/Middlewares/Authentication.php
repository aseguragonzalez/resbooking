<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\Location;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Mvc\Security\IdentityManager;

final class Authentication extends Middleware
{
    public function __construct(
        private readonly AuthSettings $settings,
        private readonly IdentityManager $identityManager,
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
        /** @var string $token */
        $token = $request->getCookieParams()[$this->settings->cookieName] ?? '';
        try {
            $identity = $this->identityManager->getIdentity($token);
            /** @var RequestContext $context */
            $context = $request->getAttribute(RequestContext::class);
            $context->setIdentity($identity);
            $context->setIdentityToken($token);
            return $this->next->handleRequest($request);
        } catch (SessionExpiredException) {
            $setCookieHeader = SetCookie::removeCookie($this->settings->cookieName);
            $locationHeader = Location::toInternalUrl($this->settings->signInPath);
            return $this->responseFactory
                ->createResponse(StatusCode::SeeOther->value)
                ->withHeader($locationHeader->name, $locationHeader->value)
                ->withHeader($setCookieHeader->name, $setCookieHeader->value);
        }
    }
}
