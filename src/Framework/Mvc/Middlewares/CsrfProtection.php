<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Requests\RequestContextKeys;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Responses\StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CsrfProtection extends Middleware
{
    private const CONTEXT_KEY = 'csrf_token';

    /**
     * @param array<string> $protectedMethods
     */
    public function __construct(
        private readonly string $cookieName,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $protectedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'],
        ?Middleware $next = null,
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        /** @var ?RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        if ($context === null) {
            throw new \RuntimeException('RequestContext not found in request attributes');
        }

        $method = strtoupper($request->getMethod());
        $token = $this->getOrCreateToken($request, $context);

        if (!in_array($method, $this->protectedMethods, true)) {
            return $this->next->handleRequest($request);
        }

        $requestToken = $this->getTokenFromRequest($request);
        if (!is_string($requestToken) || $requestToken === '' || !hash_equals($token, $requestToken)) {
            $response = $this->responseFactory->createResponse(StatusCode::Forbidden->value);
            $response->getBody()->write('Invalid CSRF token');
            return $response;
        }

        return $this->next->handleRequest($request);
    }

    private function getOrCreateToken(ServerRequestInterface $request, RequestContext $context): string
    {
        $cookies = $request->getCookieParams();
        $existing = $cookies[$this->cookieName] ?? null;
        if (is_string($existing) && $existing !== '') {
            $context->set(self::CONTEXT_KEY, $existing);
            return $existing;
        }

        $token = bin2hex(random_bytes(32));
        $context->set(self::CONTEXT_KEY, $token);

        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->cookieName,
            cookieValue: $token,
            expires: -1,
        );

        $response = $this->responseFactory->createResponse(StatusCode::Ok->value)
            ->withAddedHeader($setCookieHeader->name, $setCookieHeader->value);

        return $token;
    }

    private function getTokenFromRequest(ServerRequestInterface $request): ?string
    {
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody) && isset($parsedBody['_csrf']) && is_string($parsedBody['_csrf'])) {
            return $parsedBody['_csrf'];
        }

        $headers = $request->getHeader('X-CSRF-Token');
        if (!empty($headers)) {
            /** @var string $header */
            $header = $headers[0];
            return $header;
        }

        return null;
    }

    public static function getTokenFromContext(RequestContext $context): string
    {
        /** @var string $token */
        $token = $context->get(self::CONTEXT_KEY);
        return $token;
    }
}
