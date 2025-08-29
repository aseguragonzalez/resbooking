<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Middlewares\ErrorHandling;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Application\Logging\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Seedwork\Infrastructure\Mvc\Views\ViewEngine;
use Seedwork\Infrastructure\Mvc\ErrorMapping;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;

class ErrorHandlingTest extends TestCase
{
    private ErrorHandling $middleware;
    private Logger $logger;
    private ResponseFactoryInterface $responseFactory;
    private ViewEngine $viewEngine;
    private Settings $settings;
    private Psr17Factory $psrFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseFactory = new Psr17Factory();
        $this->viewEngine = $this->createMock(ViewEngine::class);
        $this->settings = new Settings(
            basePath: __DIR__,
            errorsMapping: [\InvalidArgumentException::class => new ErrorMapping(400, 'custom_error', 'custom_error')],
            errorsMappingDefaultValue: new ErrorMapping(500, 'error', 'error')
        );
        $this->psrFactory = new Psr17Factory();
        $this->middleware = new ErrorHandling(
            settings: $this->settings,
            logger: $this->logger,
            responseFactory: $this->responseFactory,
            viewEngine: $this->viewEngine
        );
    }

    public function testThrowsIfNoNextMiddleware(): void
    {
        $request = $this->psrFactory->createServerRequest('GET', '/')
            ->withAttribute(RequestContext::class, new RequestContext());

        $this->expectException(\RuntimeException::class);
        $this->middleware->handleRequest($request);
    }

    public function testHandlesExceptionWithCustomMapping(): void
    {
        $next = $this->createMock(Middleware::class);
        $next->method('handleRequest')
            ->willThrowException(new \InvalidArgumentException('Test'));
        $this->middleware->setNext($next);
        $request = $this->psrFactory->createServerRequest('GET', '/')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testHandlesExceptionWithDefaultMapping(): void
    {
        $next = $this->createMock(Middleware::class);
        $next->method('handleRequest')
            ->willThrowException(new \Exception('Test'));
        $this->middleware->setNext($next);

        $request = $this->psrFactory->createServerRequest('GET', '/')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
