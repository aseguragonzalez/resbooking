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
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tests\Unit\Framework\Mvc\Fixtures\Routes\Route\RouteController;

final class AuthorizationTest extends TestCase
{
    private const PUBLIC_ROUTE = '/public';
    private const PRIVATE_ROUTE = '/private';
    private const NO_ROLES_ROUTE = '/no-roles';
    private const ADMIN_ROLE = 'admin';
    private const USER_ROLE = 'user';
    private Authorization $middleware;
    private AuthSettings $settings;

    protected function setUp(): void
    {
        $this->settings = new AuthSettings(
            cookieName: 'auth_token',
            signInPath: '/login',
            signOutPath: '/logout',
        );
        $this->middleware = new Authorization(
            settings: $this->settings,
            responseFactory: new Psr17Factory(),
            router: self::createRouter(),
        );
    }

    private static function createRouter(): Router
    {
        $route = Route::create(
            RouteMethod::Get,
            Path::create(self::PRIVATE_ROUTE),
            RouteController::class,
            'get',
            true,
            [self::ADMIN_ROLE, self::USER_ROLE]
        );
        $publicRoute = Route::create(
            RouteMethod::Get,
            Path::create(self::PUBLIC_ROUTE),
            RouteController::class,
            'get',
            false,
            []
        );
        $noRolesRoute = Route::create(
            RouteMethod::Get,
            Path::create(self::NO_ROLES_ROUTE),
            RouteController::class,
            'get',
            true,
            []
        );
        return new Router([$route, $publicRoute, $noRolesRoute]);
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $request = $this->createRequestStub(path: self::PUBLIC_ROUTE);
        $this->expectException(\RuntimeException::class);

        $this->middleware->handleRequest($request);
    }

    public function testHandleRequestEnsureAuthenticatedAndAuthorizedUser(): void
    {
        $request = $this->createRequestStub(self::PRIVATE_ROUTE, true, [self::ADMIN_ROLE, self::USER_ROLE]);
        $response = $this->createStub(ResponseInterface::class);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->once())->method('handleRequest')->with($request)->willReturn($response);
        $this->middleware->setNext($next);

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestRedirectsWhenNotAuthenticated(): void
    {
        $request = $this->createRequestStub(path: self::PRIVATE_ROUTE);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->never())->method('handleRequest');
        $this->middleware->setNext($next);

        $result = $this->middleware->handleRequest($request);

        $this->assertEquals(303, $result->getStatusCode());
        $this->assertEquals($this->settings->signInPath, $result->getHeaderLine('Location'));
        $this->assertStringContainsString('auth_token=;', $result->getHeaderLine('Set-Cookie'));
    }

    public function testHandleRequestPassesThroughWhenRouteNotRequiringAuth(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $request = $this->createRequestStub(path: self::PUBLIC_ROUTE);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->once())->method('handleRequest')->with($request)->willReturn($response);
        $this->middleware->setNext($next);

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestThrowsAccessDeniedWhenUserHasNoRoles(): void
    {
        $request = $this->createRequestStub(path: self::PRIVATE_ROUTE, isAuthenticated: true, roles: []);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->never())->method('handleRequest');
        $this->middleware->setNext($next);
        $this->expectException(AccessDeniedException::class);

        $this->middleware->handleRequest($request);
    }

    public function testHandleRequestThrowsAccessDeniedWhenRolesMismatch(): void
    {
        $request = $this->createRequestStub(path: self::PRIVATE_ROUTE, isAuthenticated: true, roles: ['guest']);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->never())->method('handleRequest');
        $this->middleware->setNext($next);
        $this->expectException(AccessDeniedException::class);

        $this->middleware->handleRequest($request);
    }

    public function testHandleRequestPassesThroughWhenRouteHasNoRoleRequirements(): void
    {
        $request = $this->createRequestStub(path: self::NO_ROLES_ROUTE, isAuthenticated: true, roles: ['any-role']);
        $response = $this->createStub(ResponseInterface::class);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->once())->method('handleRequest')->with($request)->willReturn($response);
        $this->middleware->setNext($next);

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    public function testHandleRequestPassesThroughWhenUserHasPartialRoleMatch(): void
    {
        $request = $this->createRequestStub(path: self::PRIVATE_ROUTE, isAuthenticated: true, roles: [self::USER_ROLE]);
        $response = $this->createStub(ResponseInterface::class);
        $next = $this->createMock(Middleware::class);
        $next->expects($this->once())->method('handleRequest')->with($request)->willReturn($response);
        $this->middleware->setNext($next);

        $result = $this->middleware->handleRequest($request);

        $this->assertSame($response, $result);
    }

    /**
     * @param string $path
     * @param bool $isAuthenticated
     * @param array<string> $roles
     * @return ServerRequestInterface
     */
    private function createRequestStub(
        string $path,
        bool $isAuthenticated = false,
        array $roles = [],
    ): ServerRequestInterface {
        $identity = $this->createStub(Identity::class);
        $identity->method('isAuthenticated')->willReturn($isAuthenticated);
        $identity->method('getRoles')->willReturn($roles);

        $context = new RequestContext();
        $context->setIdentity($identity);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getPath')->willReturn($path);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getAttribute')->with(RequestContext::class)->willReturn($context);
        return $request;
    }
}
