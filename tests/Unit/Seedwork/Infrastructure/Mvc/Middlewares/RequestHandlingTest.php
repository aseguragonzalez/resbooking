<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use Seedwork\Infrastructure\Mvc\Middlewares\RequestHandling;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;

class RequestHandlingTest extends TestCase
{
    private Psr17Factory $requestFactory;
    private RequestHandling $middleware;
    private RequestHandlerInterface&MockObject $requestHandlerMock;

    protected function setUp(): void
    {
        $this->requestFactory = new Psr17Factory();
        $this->requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $this->middleware = new RequestHandling(
            requestHandler: $this->requestHandlerMock,
        );
    }

    public function testHandleRequestReturnsResponse(): void
    {
        $request = $this->requestFactory->createServerRequest('GET', '/any-uri');
        $expectedResponse = $this->requestFactory->createResponse(200);
        $this->requestHandlerMock->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expectedResponse);

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }


    public function testHandleRequestPassesRequestToHandler(): void
    {
        $request = $this->requestFactory->createServerRequest('POST', '/submit');
        $expectedResponse = $this->requestFactory->createResponse(201);
        $this->requestHandlerMock->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expectedResponse);

        $response = $this->middleware->handleRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }


    public function testHandleRequestWithDifferentResponse(): void
    {
        $request = $this->requestFactory->createServerRequest('DELETE', '/delete');
        $expectedResponse = $this->requestFactory->createResponse(204);
        $this->requestHandlerMock->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expectedResponse);

        $response = $this->middleware->handleRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
