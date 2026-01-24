<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Middlewares;

use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Middlewares\Localization;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Responses\StatusCode;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[AllowMockObjectsWithoutExpectations]
final class LocalizationTest extends TestCase
{
    private Psr17Factory $requestFactory;
    private Localization $middleware;
    private string $cookieName = 'lang';
    private string $defaultValue = 'en';

    protected function setUp(): void
    {
        $this->requestFactory = new Psr17Factory();
        $mock = $this->createMock(Middleware::class);
        $mock->method('handleRequest')->willReturn($this->requestFactory->createResponse(200));
        $this->middleware = new Localization(
            settings: new LanguageSettings(
                basePath: __DIR__,
                assetsPath: 'assets/i18n',
                languages: ['en', 'es', 'fr'],
                cookieName: $this->cookieName,
                defaultValue: $this->defaultValue,
                setUrl: '/set-language',
            ),
            responseFactory: new Psr17Factory(),
            next: $mock,
        );
    }

    protected function tearDown(): void
    {
    }

    public function testHandleRequestSetsLanguageCookieOnPost(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('POST', '/set-language')
            ->withParsedBody(['language' => 'es'])
            ->withAddedHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withAddedHeader('Accept-Language', 'es')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->cookieName,
            cookieValue: 'es',
            expires: -1,
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Found->value, $response->getStatusCode());
        $this->assertEquals($setCookieHeader->value, $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals('es', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestSetsDefaultLanguageFromBodyOnPost(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('POST', '/set-language')
            ->withParsedBody([])
            ->withAddedHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withAddedHeader('Accept-Language', 'fr')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->cookieName,
            cookieValue: $this->defaultValue,
            expires: -1,
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Found->value, $response->getStatusCode());
        $this->assertEquals($setCookieHeader->value, $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($this->defaultValue, $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestSetsDefaultLanguageOnInvalidPost(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('POST', '/set-language')
            ->withParsedBody(['language' => 'xx'])
            ->withAddedHeader('Accept-Language', 'es')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->cookieName,
            cookieValue: $this->defaultValue,
            expires: -1,
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Found->value, $response->getStatusCode());
        $this->assertEquals($setCookieHeader->value, $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($this->defaultValue, $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithValidLanguageCookie(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withCookieParams([$this->cookieName => 'es'])
            ->withAddedHeader('Accept-Language', 'fr;q=0.8')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Ok->value, $response->getStatusCode());
        $this->assertEquals('es', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithNoLanguageCookieFallsBackToHeader(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withAddedHeader('Accept-Language', 'fr;q=0.8')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Ok->value, $response->getStatusCode());
        $this->assertEquals('fr', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithNoLanguageHeaderUsesDefault(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Ok->value, $response->getStatusCode());
        $this->assertEquals($this->defaultValue, $response->getHeaderLine('Content-Language'));
    }

    public function testLocalizationMiddlewareFailsIfNoRequestContext(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('RequestContext not found in request attributes');
        $request = $this->requestFactory->createServerRequest('GET', '/any-uri');
        $this->middleware->handleRequest($request);
    }

    public function testLocalizationMiddlewareFailsIfNoNextMiddleware(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No middleware to handle the request');
        $middleware = new Localization(
            settings: new LanguageSettings(
                basePath: __DIR__,
                assetsPath: 'assets/i18n',
                languages: ['en', 'es', 'fr'],
                cookieName: $this->cookieName,
                defaultValue: $this->defaultValue,
                setUrl: '/set-language',
            ),
            responseFactory: new Psr17Factory(),
            next: null,
        );
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withAttribute(RequestContext::class, new RequestContext());
        $middleware->handleRequest($request);
    }
}
