<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\AuthSettings;
use Framework\Mvc\Middlewares\Authorization;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\AccessDeniedException;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Routes\Router;
use Framework\Mvc\Security\Identity;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tests\Unit\Framework\Mvc\Fixtures\Routes\Route\RouteController;

#[AllowMockObjectsWithoutExpectations]
final class AuthorizationTest extends TestCase
{
    private Authorization $middleware;
    private AuthSettings $settings;
    private Middleware&MockObject $next;
    private Psr17Factory $psrFactory;
    private Router $router;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->settings = new AuthSettings(
            cookieName: 'auth_token',
            signInPath: '/login',
            signOutPath: '/logout',
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
        $this->middleware = new Authorization(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            router: $this->router,
            next: $this->next,
        );
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
        $request->method('getAttribute')->with(RequestContext::class)->willReturn($context);

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new Authorization(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            router: $this->router,
            next: null,
        );
        $request = $this->createMock(ServerRequestInterface::class);
        $this->expectException(\RuntimeException::class);
        $middleware->handleRequest($request);
    }

    public function testHandleRequestRedirectsWhenNotAuthenticated(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next->expects($this->never())->method('handleRequest');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(false);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')->with(RequestContext::class)->willReturn($context);

        $result = $this->middleware->handleRequest($request);

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
        $middleware = new Authorization(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            router: $router,
            next: $this->next,
        );

        $result = $middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsAccessDeniedWhenUserHasNoRoles(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next->expects($this->never())->method('handleRequest');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('getRoles')->willReturn([]);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/foo');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')->with(RequestContext::class)->willReturn($context);
        $this->expectException(AccessDeniedException::class);

        $this->middleware->handleRequest($request);
    }

    public function testHandleRequestThrowsAccessDeniedWhenRolesMismatch(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->next->expects($this->never())->method('handleRequest');
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
        $this->expectException(AccessDeniedException::class);

        $this->middleware->handleRequest($request);
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
        $middleware = new Authorization(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            router: $router,
            next: $this->next,
        );

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

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }
}
