<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\AuthSettings;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Security\Challenge;
use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\ChallengesExpirationTime;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\IdentityStore;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

final class AuthenticationTest extends TestCase
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
        $store = $this->createStub(IdentityStore::class);
        $notificator = $this->createStub(ChallengeNotificator::class);
        $expiration = new ChallengesExpirationTime(10, 5, 20, 15, 30);
        $this->identityManager = new DefaultIdentityManager($notificator, $expiration, $store);
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willReturn($this->psrFactory->createResponse(200));
        $this->next = $next;
    }

    public function testHandleRequestWithValidTokenSetsIdentityAndToken(): void
    {
        $user = UserIdentity::new('user@domain.com', ['ROLE_USER'], 'password')->activate();
        $token = 'valid_token';
        $store = $this->createStub(IdentityStore::class);
        $store->method('getSignInSessionByToken')->willReturn(
            \Framework\Mvc\Security\Domain\Entities\SignInSession::build(
                $this->createStub(Challenge::class),
                $user
            )
        );
        $identityManager = new DefaultIdentityManager(
            $this->createStub(ChallengeNotificator::class),
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
        $store = $this->createStub(IdentityStore::class);
        $store->method('getSignInSessionByToken')->willReturn(null);
        $identityManager = new DefaultIdentityManager(
            $this->createStub(ChallengeNotificator::class),
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
