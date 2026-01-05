<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\AuthSettings;
use Seedwork\Infrastructure\Mvc\Middlewares\Authentication;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Security\Challenge;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\ChallengesExpirationTime;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;

class AuthenticationTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private AuthSettings $settings;
    private RequestContext $context;
    private DefaultIdentityManager $identityManager;
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
        $store = $this->createMock(IdentityStore::class);
        $store->method('getSignInSessionByToken')->willReturn(null);
        $identityManager = new DefaultIdentityManager(
            $this->createMock(ChallengeNotificator::class),
            new ChallengesExpirationTime(10, 5, 20, 15, 30),
            $store
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
