<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Middlewares;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Security\Identity;

final class RestaurantContextTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private DashboardSettings $settings;
    private MockObject&RestaurantRepository $restaurantRepository;
    private MockObject&Middleware $next;

    protected function setUp(): void
    {
        $this->psrFactory = new Psr17Factory();
        $this->settings = new DashboardSettings(
            basePath: __DIR__,
            restaurantCookieName: 'restaurant',
            restaurantSelectionUrl: '/restaurants/select',
            restaurantIdContextKey: 'restaurantId',
        );
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->next = $this->createMock(Middleware::class);
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: null
        );

        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('username')->willReturn('user@example.com');
        $context = new RequestContext();
        $context->setIdentity($identity);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute(RequestContext::class, $context);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No middleware to handle the request');
        $middleware->handleRequest($request);
    }

    public function testHandleRequestPassesThroughWhenIdentityNotAuthenticated(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(false);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $expectedResponse = $this->psrFactory->createResponse(200);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute(RequestContext::class, $context);

        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($expectedResponse);

        $this->restaurantRepository
            ->expects($this->never())
            ->method('findByUserEmail');

        $response = $middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testHandleRequestPassesThroughWhenPathIsRestaurantSelectionUrl(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $expectedResponse = $this->psrFactory->createResponse(200);
        $request = (new ServerRequest('GET', $this->settings->restaurantSelectionUrl))
            ->withAttribute(RequestContext::class, $context);

        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($expectedResponse);

        $this->restaurantRepository
            ->expects($this->never())
            ->method('findByUserEmail');

        $response = $middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testHandleRequestSetsRestaurantIdInContextWhenCookieIsValid(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $restaurantId = 'restaurant-123';
        $userEmail = 'user@example.com';
        $restaurant = Restaurant::new($userEmail, $restaurantId);
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('username')->willReturn($userEmail);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $expectedResponse = $this->psrFactory->createResponse(200);
        $request = (new ServerRequest('GET', '/dashboard'))
            ->withCookieParams([$this->settings->restaurantCookieName => $restaurantId])
            ->withAttribute(RequestContext::class, $context);

        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($userEmail)
            ->willReturn([$restaurant]);

        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($expectedResponse);

        $response = $middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
        $this->assertSame($restaurantId, $context->get($this->settings->restaurantIdContextKey));
    }

    public function testHandleRequestRedirectsWhenCookieIsInvalid(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $invalidRestaurantId = 'invalid-restaurant-123';
        $userEmail = 'user@example.com';
        $validRestaurant = Restaurant::new($userEmail, 'valid-restaurant-456');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('username')->willReturn($userEmail);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $request = (new ServerRequest('GET', '/dashboard'))
            ->withCookieParams([$this->settings->restaurantCookieName => $invalidRestaurantId])
            ->withAttribute(RequestContext::class, $context);

        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($userEmail)
            ->willReturn([$validRestaurant]);

        $this->next
            ->expects($this->never())
            ->method('handleRequest');

        $response = $middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        $selectionUrl = $this->settings->restaurantSelectionUrl;
        assert($selectionUrl !== '');
        $this->assertStringStartsWith($selectionUrl, $response->getHeaderLine('Location'));
    }

    public function testHandleRequestRedirectsWhenCookieIsMissing(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $userEmail = 'user@example.com';
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('username')->willReturn($userEmail);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $request = (new ServerRequest('GET', '/dashboard'))
            ->withCookieParams([])
            ->withAttribute(RequestContext::class, $context);

        $this->restaurantRepository
            ->expects($this->never())
            ->method('findByUserEmail');

        $this->next
            ->expects($this->never())
            ->method('handleRequest');

        $response = $middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        $selectionUrl = $this->settings->restaurantSelectionUrl;
        assert($selectionUrl !== '');
        $this->assertStringStartsWith($selectionUrl, $response->getHeaderLine('Location'));
    }

    public function testHandleRequestRedirectsWithCorrectBackUrl(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );

        $userEmail = 'user@example.com';
        $backUrl = 'https://example.com/dashboard/reservations?filter=active';
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(true);
        $identity->method('username')->willReturn($userEmail);
        $context = new RequestContext();
        $context->setIdentity($identity);
        $request = (new ServerRequest('GET', $backUrl))
            ->withCookieParams([])
            ->withAttribute(RequestContext::class, $context);

        $this->restaurantRepository
            ->expects($this->never())
            ->method('findByUserEmail');

        $this->next
            ->expects($this->never())
            ->method('handleRequest');

        $response = $middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        $location = $response->getHeaderLine('Location');
        $selectionUrl = $this->settings->restaurantSelectionUrl;
        assert($selectionUrl !== '');
        $this->assertStringStartsWith($selectionUrl, $location);
        $this->assertStringContainsString('backUrl=', $location);
        $this->assertStringContainsString(urlencode($backUrl), $location);
    }
}
