<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\AuthSettings;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Mvc\Security\IdentityManager;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

final class AuthenticationTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private AuthSettings $settings;
    private RequestContext $context;
    private IdentityManager $identityManager;
    private Middleware $next;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->settings = new AuthSettings(
            cookieName: 'auth_token',
            signInPath: '/login',
            signOutPath: '/logout',
        );
        $this->context = new RequestContext();
        $this->identityManager = $this->createStub(IdentityManager::class);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willReturn($this->psrFactory->createResponse(200));
        $this->next = $next;
    }

    public function testHandleRequestWithValidTokenSetsIdentityAndToken(): void
    {
        $user = UserIdentity::new('user@domain.com', ['ROLE_USER'], 'password')->activate();
        $token = 'valid_token';
        $identityManager = $this->createStub(IdentityManager::class);
        $identityManager->method('getIdentity')->willReturn($user);
        $middleware = new Authentication(
            settings: $this->settings,
            identityManager: $identityManager,
            responseFactory: $this->psrFactory,
            next: $this->next,
        );
        $middleware->setNext($this->next);

        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => $token])
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);
        $this->assertEquals(StatusCode::Ok->value, $response->getStatusCode());
        $this->assertSame($user, $this->context->getAs('identity', UserIdentity::class));
        $this->assertSame($token, $this->context->get('identity_token'));
    }

    public function testHandleRequestWithExpiredSessionRedirects(): void
    {
        $identityManager = $this->createStub(IdentityManager::class);
        $identityManager->method('getIdentity')->willThrowException(
            new SessionExpiredException()
        );
        $middleware = new Authentication(
            settings: $this->settings,
            identityManager: $identityManager,
            responseFactory: $this->psrFactory,
            next: $this->next,
        );
        $middleware->setNext($this->next);

        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => 'expired_token'])
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);
        $this->assertEquals(StatusCode::SeeOther->value, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
        $this->assertStringContainsString('auth_token=;', $response->getHeaderLine('Set-Cookie'));
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new Authentication(
            settings: $this->settings,
            identityManager: $this->identityManager,
            responseFactory: $this->psrFactory,
            next: null,
        );
        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => 'any'])
            ->withAttribute(RequestContext::class, $this->context);
        $this->expectException(\RuntimeException::class);

        $middleware->handleRequest($request);
    }
}
