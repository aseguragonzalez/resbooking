<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Middlewares;

use Framework\Middlewares\AllowedHttpMethodsForHtmlUi;
use Framework\Middlewares\Middleware;
use Framework\Middlewares\RequestHandling;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AllowedHttpMethodsForHtmlUiTest extends TestCase
{
    private Psr17Factory $psr17;

    protected function setUp(): void
    {
        $this->psr17 = new Psr17Factory();
    }

    public function testOptionsReturns204WithAllowHeader(): void
    {
        $inner = $this->createInnerMiddleware();
        $middleware = new AllowedHttpMethodsForHtmlUi($this->psr17, next: $inner);
        $request = $this->psr17->createServerRequest('OPTIONS', '/any');

        $response = $middleware->handleRequest($request);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('GET, POST, HEAD, OPTIONS', $response->getHeaderLine('Allow'));
    }

    public function testDeleteReturns405WithAllowAndDoesNotCallNext(): void
    {
        $state = new \stdClass();
        $state->called = false;
        $inner = new class ($state) extends Middleware {
            public function __construct(private \stdClass $state)
            {
                parent::__construct(null);
            }

            public function handleRequest(\Psr\Http\Message\ServerRequestInterface $request): ResponseInterface
            {
                $this->state->called = true;

                throw new \RuntimeException('next should not run');
            }
        };
        $middleware = new AllowedHttpMethodsForHtmlUi($this->psr17, next: $inner);
        $request = $this->psr17->createServerRequest('DELETE', '/resource');

        $response = $middleware->handleRequest($request);

        $this->assertFalse($state->called);
        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET, POST, HEAD, OPTIONS', $response->getHeaderLine('Allow'));
        $this->assertStringContainsString('Method Not Allowed', (string) $response->getBody());
    }

    public function testGetDelegatesToNext(): void
    {
        $inner = new RequestHandling(
            requestHandler: new class () implements RequestHandlerInterface {
                public function handle(\Psr\Http\Message\ServerRequestInterface $request): ResponseInterface
                {
                    return (new Psr17Factory())->createResponse(200);
                }
            },
        );
        $middleware = new AllowedHttpMethodsForHtmlUi($this->psr17, next: $inner);
        $request = $this->psr17->createServerRequest('GET', '/');

        $response = $middleware->handleRequest($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    private function createInnerMiddleware(): Middleware
    {
        return new RequestHandling(
            requestHandler: new class () implements RequestHandlerInterface {
                public function handle(\Psr\Http\Message\ServerRequestInterface $request): ResponseInterface
                {
                    throw new \RuntimeException('inner should not run for OPTIONS');
                }
            },
        );
    }
}
