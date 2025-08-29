<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Middlewares\Tests;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Middlewares\Localization;
use Seedwork\Infrastructure\Mvc\Settings;
use Psr\Http\Message\ResponseInterface;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

class LocalizationTest extends TestCase
{
    private Psr17Factory $requestFactory;
    private Localization $middleware;
    private string $languageCookieName = 'lang';
    private string $languageDefaultValue = 'en_US';

    protected function setUp(): void
    {
        $this->requestFactory = new Psr17Factory();
        $mock = $this->createMock(Middleware::class);
        $mock->method('handleRequest')->willReturn($this->requestFactory->createResponse(200));
        $this->middleware = new Localization(
            settings: new Settings(
                basePath: __DIR__,
                languages: ['en_US', 'es_ES', 'fr_FR'],
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
            ->withParsedBody(['language' => 'es_ES'])
            ->withAddedHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withAddedHeader('Accept-Language', 'es_ES')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals("{$this->languageCookieName}=es_ES", $response->getHeaderLine('Set-Cookie'));
        $this->assertEquals('es_ES', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestSetsDefaultLanguageOnInvalidPost(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('POST', '/set-language')
            ->withParsedBody(['language' => 'xx_XX'])
            ->withAddedHeader('Accept-Language', 'es_ES')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(
            "{$this->languageCookieName}={$this->languageDefaultValue}",
            $response->getHeaderLine('Set-Cookie')
        );
        $this->assertEquals($this->languageDefaultValue, $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithValidLanguageCookie(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withCookieParams([$this->languageCookieName => 'es_ES'])
            ->withAddedHeader('Accept-Language', 'fr_FR;q=0.8')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('es_ES', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithNoLanguageCookieFallsBackToHeader(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withAddedHeader('Accept-Language', 'fr_FR;q=0.8')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('fr_FR', $response->getHeaderLine('Content-Language'));
    }

    public function testHandleRequestWithNoLanguageHeaderUsesDefault(): void
    {
        $request = $this->requestFactory
            ->createServerRequest('GET', '/any-uri')
            ->withAttribute(RequestContext::class, new RequestContext());

        $response = $this->middleware->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->languageDefaultValue, $response->getHeaderLine('Content-Language'));
    }
}
