<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Seedwork\Infrastructure\Mvc\Middlewares\Authorization;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Security\Identity;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route\RouteController;

final class AuthorizationTest extends TestCase
{
    public function testHandleRequestEnsureAuthenticatedAndAuthorizedUser(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/foo'),
            RouteController::class,
            'get',
            true,
            ['admin', 'user']
        );
        $router = new Router([$route]);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($response);
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn(['admin', 'user']);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($router, $next);

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new Authorization(new Router(), null);

        $request = $this->createMock(ServerRequestInterface::class);
        $this->expectException(\RuntimeException::class);
        $middleware->handleRequest($request);
    }
}
