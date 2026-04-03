<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\Middlewares\ErrorHandling;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Views\ViewEngine;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class ErrorHandlingTest extends TestCase
{
    private ErrorHandling $middleware;
    private ErrorSettings $settings;
    private LoggerInterface&MockObject $logger;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $psrFactory = new Psr17Factory();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->settings = new ErrorSettings(
            errorsMapping: [\InvalidArgumentException::class => new ErrorMapping(400, 'custom_error', 'custom_error')],
            errorsMappingDefaultValue: new ErrorMapping(500, 'error', 'error')
        );
        $this->request = $psrFactory
            ->createServerRequest('GET', '/')
            ->withAttribute(RequestContext::class, new RequestContext());
        $this->middleware = new ErrorHandling(
            settings: $this->settings,
            logger: $this->logger,
            responseFactory: $psrFactory,
            viewEngine: $this->createStub(ViewEngine::class)
        );
    }

    public function testThrowsIfNoNextMiddleware(): void
    {
        $this->logger->expects($this->never())->method('error');
        $this->expectException(\RuntimeException::class);

        $this->middleware->handleRequest($this->request);
    }

    public function testHandlesExceptionWithCustomMapping(): void
    {
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Error handling middleware: {message}', ['message' => 'Test']);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willThrowException(new \InvalidArgumentException('Test'));
        $this->middleware->setNext($next);

        $response = $this->middleware->handleRequest($this->request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::BadRequest->value, $response->getStatusCode());
    }

    public function testHandlesExceptionWithDefaultMapping(): void
    {
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Error handling middleware: {message}', ['message' => 'Test']);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willThrowException(new \Exception('Test'));
        $this->middleware->setNext($next);

        $response = $this->middleware->handleRequest($this->request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::InternalServerError->value, $response->getStatusCode());
    }
}
