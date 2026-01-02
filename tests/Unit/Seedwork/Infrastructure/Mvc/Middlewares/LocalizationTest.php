<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Seedwork\Infrastructure\Mvc\Middlewares\Localization;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Settings;

class LocalizationTest extends TestCase
{
    private Psr17Factory $requestFactory;
    private Localization $middleware;
    private string $languageCookieName = 'lang';
    private string $languageDefaultValue = 'en';

    protected function setUp(): void
    {
        $this->requestFactory = new Psr17Factory();
        $mock = $this->createMock(Middleware::class);
        $mock->method('handleRequest')->willReturn($this->requestFactory->createResponse(200));
        $this->middleware = new Localization(
            settings: new Settings(
                basePath: __DIR__,
                languages: ['en', 'es', 'fr'],
                languageCookieName: $this->languageCookieName,
                languageDefaultValue: $this->languageDefaultValue,
                languageSetUrl: '/set-language',
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
            cookieName: $this->languageCookieName,
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
            cookieName: $this->languageCookieName,
            cookieValue: $this->languageDefaultValue,
            expires: -1,
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Found->value, $response->getStatusCode());
        $this->assertEquals($setCookieHeader->value, $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($this->languageDefaultValue, $response->getHeaderLine('Content-Language'));
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
            cookieName: $this->languageCookieName,
            cookieValue: $this->languageDefaultValue,
            expires: -1,
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(StatusCode::Found->value, $response->getStatusCode());
        $this->assertEquals($setCookieHeader->value, $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($this->languageDefaultValue, $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithValidLanguageCookie(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withCookieParams([$this->languageCookieName => 'es'])
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
        $this->assertEquals($this->languageDefaultValue, $response->getHeaderLine('Content-Language'));
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
            settings: new Settings(
                basePath: __DIR__,
                languages: ['en', 'es', 'fr'],
                languageCookieName: $this->languageCookieName,
                languageDefaultValue: $this->languageDefaultValue,
                languageSetUrl: '/set-language',
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
