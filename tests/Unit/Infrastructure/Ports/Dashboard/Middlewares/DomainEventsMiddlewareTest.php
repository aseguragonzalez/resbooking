<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Middlewares;

use Framework\Mvc\Middlewares\Middleware;
use Infrastructure\Ports\Dashboard\Middlewares\DomainEventsMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Application\Messaging\DomainEventsBus;

final class DomainEventsMiddlewareTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private MockObject&DomainEventsBus $domainEventsBus;
    private MockObject&Middleware $next;
    private DomainEventsMiddleware $middleware;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->domainEventsBus = $this->createMock(DomainEventsBus::class);
        $this->next = $this->createMock(Middleware::class);
        $this->middleware = new DomainEventsMiddleware(
            domainEventsBus: $this->domainEventsBus,
            next: $this->next
        );
    }

    public function testCallsNextThenNotifyThenReturnsResponse(): void
    {
        $request = new ServerRequest('GET', '/');
        $expectedResponse = $this->psrFactory->createResponse(200);
        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($expectedResponse);
        $this->domainEventsBus
            ->expects($this->once())
            ->method('notify');

        $response = $this->middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testThrowsWhenNextIsNull(): void
    {
        $middleware = new DomainEventsMiddleware(
            domainEventsBus: $this->domainEventsBus,
            next: null
        );
        $this->domainEventsBus->expects($this->never())->method('notify');
        $this->next->expects($this->never())->method('handleRequest');
        $request = new ServerRequest('GET', '/');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No middleware to handle the request');

        $middleware->handleRequest($request);
    }
}
