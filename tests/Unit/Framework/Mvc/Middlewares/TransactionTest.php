<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Middlewares\Transaction;
use Framework\Mvc\Requests\RequestContext;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private PDO&MockObject $pdo;
    private RequestContext $context;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->pdo = $this->createMock(PDO::class);
        $this->context = new RequestContext();
    }

    public function testNonPostRequestDelegatesWithoutTransaction(): void
    {
        $this->pdo->expects($this->never())->method('beginTransaction');
        $this->pdo->expects($this->never())->method('commit');
        $this->pdo->expects($this->never())->method('rollBack');
        $expectedResponse = $this->psrFactory->createResponse(200);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willReturn($expectedResponse);
        $middleware = new Transaction($this->pdo, $next);
        $middleware->setNext($next);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testPostRequestCommitsOnSuccess(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');
        $this->pdo->expects($this->never())->method('rollBack');
        $expectedResponse = $this->psrFactory->createResponse(201);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willReturn($expectedResponse);
        $middleware = new Transaction($this->pdo, $next);
        $middleware->setNext($next);
        $request = (new ServerRequest('POST', '/'))
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testPostRequestRollsBackOnException(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->never())->method('commit');
        $this->pdo->expects($this->once())->method('rollBack');
        $exception = new \RuntimeException('Something went wrong');
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willThrowException($exception);
        $middleware = new Transaction($this->pdo, $next);
        $request = (new ServerRequest('POST', '/'))
            ->withAttribute(RequestContext::class, $this->context);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong');

        $middleware->handleRequest($request);
    }

    public function testThrowsIfNoNextMiddleware(): void
    {
        $this->pdo->expects($this->never())->method('beginTransaction');
        $this->pdo->expects($this->never())->method('commit');
        $this->pdo->expects($this->never())->method('rollBack');
        $middleware = new Transaction($this->pdo, null);
        $request = (new ServerRequest('POST', '/'))
            ->withAttribute(RequestContext::class, $this->context);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No middleware to handle the request');

        $middleware->handleRequest($request);
    }
}
