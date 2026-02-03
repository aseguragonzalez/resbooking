<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentity;
use Framework\Mvc\Security\Application\GetIdentity\GetIdentity;
use Framework\Mvc\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPassword;
use Framework\Mvc\Security\Application\RefreshSignInSession\RefreshSignInSession;
use Framework\Mvc\Security\Application\RequestResetPassword\RequestResetPassword;
use Framework\Mvc\Security\Application\ResetPasswordFromToken\ResetPasswordFromToken;
use Framework\Mvc\Security\Application\SignIn\SignIn;
use Framework\Mvc\Security\Application\SignOut\SignOut;
use Framework\Mvc\Security\Application\SignUp\SignUp;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Security\Challenge;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
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
        $this->identityManager = new DefaultIdentityManager(
            $this->createStub(SignUp::class),
            $this->createStub(ActivateUserIdentity::class),
            $this->createStub(SignIn::class),
            $this->createStub(GetIdentity::class),
            $this->createStub(RefreshSignInSession::class),
            $this->createStub(ModifyUserIdentityPassword::class),
            $this->createStub(RequestResetPassword::class),
            $this->createStub(ResetPasswordFromToken::class),
            $this->createStub(SignOut::class)
        );
        $next = $this->createStub(Middleware::class);
        $next->method('handleRequest')->willReturn($this->psrFactory->createResponse(200));
        $this->next = $next;
    }

    public function testHandleRequestWithValidTokenSetsIdentityAndToken(): void
    {
        $user = UserIdentity::new('user@domain.com', ['ROLE_USER'], 'password')->activate();
        $token = 'valid_token';
        $getIdentity = $this->createStub(GetIdentity::class);
        $getIdentity->method('execute')->willReturn(
            SignInSession::build($this->createStub(Challenge::class), $user)->identity
        );
        $identityManager = new DefaultIdentityManager(
            $this->createStub(SignUp::class),
            $this->createStub(ActivateUserIdentity::class),
            $this->createStub(SignIn::class),
            $getIdentity,
            $this->createStub(RefreshSignInSession::class),
            $this->createStub(ModifyUserIdentityPassword::class),
            $this->createStub(RequestResetPassword::class),
            $this->createStub(ResetPasswordFromToken::class),
            $this->createStub(SignOut::class)
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
        $getIdentity = $this->createStub(GetIdentity::class);
        $getIdentity->method('execute')->willThrowException(
            new \Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException()
        );
        $identityManager = new DefaultIdentityManager(
            $this->createStub(SignUp::class),
            $this->createStub(ActivateUserIdentity::class),
            $this->createStub(SignIn::class),
            $getIdentity,
            $this->createStub(RefreshSignInSession::class),
            $this->createStub(ModifyUserIdentityPassword::class),
            $this->createStub(RequestResetPassword::class),
            $this->createStub(ResetPasswordFromToken::class),
            $this->createStub(SignOut::class)
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
