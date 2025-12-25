<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Seedwork\Infrastructure\Mvc\Middlewares\Authorization;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\AccessDeniedException;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Security\Identity;
use Seedwork\Infrastructure\Mvc\Settings;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route\RouteController;

final class AuthorizationTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private Settings $settings;
    private Router $router;
    private MockObject&Middleware $next;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->settings = new Settings(
            basePath: '',
            authCookieName: 'auth_token',
            authLoginUrl: '/login',
        );
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/foo'),
            RouteController::class,
            'get',
            true,
            ['admin', 'user']
        );
        $this->router = new Router([$route]);
        $this->next = $this->createMock(Middleware::class);
    }

    public function testHandleRequestEnsureAuthenticatedAndAuthorizedUser(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->once())
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
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, $this->next);

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, null);

        $request = $this->createMock(ServerRequestInterface::class);
        $this->expectException(\RuntimeException::class);
        $middleware->handleRequest($request);
    }

    public function testHandleRequestRedirectsWhenNotAuthenticated(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->never())
            ->method('handleRequest');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(false);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, $this->next);

        $result = $middleware->handleRequest($request);

        $this->assertEquals(303, $result->getStatusCode());
        $this->assertEquals('/login', $result->getHeaderLine('Location'));
        $this->assertStringContainsString('auth_token=;', $result->getHeaderLine('Set-Cookie'));
    }

    public function testHandleRequestPassesThroughWhenRouteNotRequiringAuth(): void
    {
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/public'),
            RouteController::class,
            'get',
            false,
            []
        );
        $router = new Router([$route]);
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($response);
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(false);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/public');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $router, $this->settings, $this->next);

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsAccessDeniedWhenUserHasNoRoles(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->never())
            ->method('handleRequest');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn([]);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, $this->next);

        $this->expectException(AccessDeniedException::class);
        $middleware->handleRequest($request);
    }

    public function testHandleRequestThrowsAccessDeniedWhenRolesMismatch(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->never())
            ->method('handleRequest');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn(['guest']);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, $this->next);

        $this->expectException(AccessDeniedException::class);
        $middleware->handleRequest($request);
    }

    public function testHandleRequestPassesThroughWhenRouteHasNoRoleRequirements(): void
    {
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/no-roles'),
            RouteController::class,
            'get',
            true,
            []
        );
        $router = new Router([$route]);
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($response);
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn(['any-role']);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/no-roles');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $router, $this->settings, $this->next);

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestPassesThroughWhenUserHasPartialRoleMatch(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($response);
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn(['user']);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($context);
        $middleware = new Authorization($this->psrFactory, $this->router, $this->settings, $this->next);

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }
}
