<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Middlewares\Authentication;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\ChallengesExpirationTime;
use Seedwork\Infrastructure\Mvc\Security\Challenge;

class AuthenticationTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private Settings $settings;
    private RequestContext $context;
    private DefaultIdentityManager $identityManager;
    private Middleware $next;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->settings = new Settings(
            basePath: '',
            authCookieName: 'auth_token',
            authLoginUrl: '/login',
        );
        $this->context = new RequestContext();
        $store = $this->createMock(IdentityStore::class);
        $notificator = $this->createMock(ChallengeNotificator::class);
        $expiration = new ChallengesExpirationTime(10, 5, 20, 15, 30);
        $this->identityManager = new DefaultIdentityManager($notificator, $expiration, $store);
        $mock = $this->createMock(Middleware::class);
        $mock->method('handleRequest')->willReturn($this->psrFactory->createResponse(200));
        $this->next = $mock;
    }

    public function testHandleRequestWithValidTokenSetsIdentityAndToken(): void
    {
        $user = UserIdentity::new('user@domain.com', ['ROLE_USER'], 'password')->activate();
        $token = 'valid_token';
        $store = $this->createMock(IdentityStore::class);
        $store->method('getSignInSessionByToken')->willReturn(
            \Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession::build(
                $this->createMock(Challenge::class),
                $user
            )
        );
        $identityManager = new DefaultIdentityManager(
            $this->createMock(ChallengeNotificator::class),
            new ChallengesExpirationTime(10, 5, 20, 15, 30),
            $store
        );
        $middleware = new Authentication($identityManager, $this->settings, $this->psrFactory);
        $reflection = new \ReflectionProperty($middleware, 'next');
        $reflection->setAccessible(true);
        $reflection->setValue($middleware, $this->next);

        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => $token])
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($user, $this->context->getAs('identity', UserIdentity::class));
        $this->assertSame($token, $this->context->get('identity_token'));
    }

    public function testHandleRequestWithExpiredSessionRedirects(): void
    {
        $store = $this->createMock(IdentityStore::class);
        $store->method('getSignInSessionByToken')->willReturn(null);
        $identityManager = new DefaultIdentityManager(
            $this->createMock(ChallengeNotificator::class),
            new ChallengesExpirationTime(10, 5, 20, 15, 30),
            $store
        );
        $middleware = new Authentication($identityManager, $this->settings, $this->psrFactory);
        $reflection = new \ReflectionProperty($middleware, 'next');
        $reflection->setAccessible(true);
        $reflection->setValue($middleware, $this->next);

        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => 'expired_token'])
            ->withAttribute(RequestContext::class, $this->context);

        $response = $middleware->handleRequest($request);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
        $this->assertStringContainsString('auth_token=;', $response->getHeaderLine('Set-Cookie'));
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new Authentication($this->identityManager, $this->settings, $this->psrFactory);
        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['auth_token' => 'any'])
            ->withAttribute(RequestContext::class, $this->context);
        $this->expectException(\RuntimeException::class);
        $middleware->handleRequest($request);
    }
}
