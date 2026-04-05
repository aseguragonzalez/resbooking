<?php

declare(strict_types=1);

namespace Framework\Middlewares;

use Framework\Responses\StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Restricts the HTTP method surface for HTML form-oriented apps: allows GET, POST, HEAD, OPTIONS;
 * responds with 405 and Allow for other methods (e.g. PUT, PATCH, DELETE).
 */
final class AllowedHttpMethodsForHtmlUi extends Middleware
{
    /** @param array<string> $allowedMethods Uppercase method names */
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $allowedMethods = ['GET', 'POST', 'HEAD', 'OPTIONS'],
        ?Middleware $next = null,
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        $method = strtoupper($request->getMethod());
        if ($method === 'OPTIONS') {
            return $this->responseFactory
                ->createResponse(StatusCode::NoContent->value)
                ->withHeader('Allow', $this->allowHeaderValue());
        }

        if (!in_array($method, $this->allowedMethods, true)) {
            $response = $this->responseFactory
                ->createResponse(StatusCode::MethodNotAllowed->value)
                ->withHeader('Allow', $this->allowHeaderValue())
                ->withHeader('Content-Type', 'text/plain; charset=utf-8');
            $response->getBody()->write('Method Not Allowed');

            return $response;
        }

        return $this->next->handleRequest($request);
    }

    private function allowHeaderValue(): string
    {
        return implode(', ', $this->allowedMethods);
    }
}
