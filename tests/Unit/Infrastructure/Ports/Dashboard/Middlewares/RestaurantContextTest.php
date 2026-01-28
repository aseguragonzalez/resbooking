<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Middlewares;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Security\Identity;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContext;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class RestaurantContextTest extends TestCase
{
    private Psr17Factory $psrFactory;
    private RestaurantContextSettings $settings;
    private MockObject&RestaurantRepository $restaurantRepository;
    private MockObject&Middleware $next;
    private RequestContext $context;
    private RestaurantContext $middleware;
    private Faker $faker;
    private MockObject&Identity $identity;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->psrFactory = new Psr17Factory();
        $this->settings = new RestaurantContextSettings();
        $this->context = new RequestContext();
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->next = $this->createMock(Middleware::class);
        $this->identity = $this->createMock(Identity::class);
        $this->middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: $this->next
        );
    }

    public function testHandleRequestThrowsIfNoNextMiddleware(): void
    {
        $middleware = new RestaurantContext(
            settings: $this->settings,
            responseFactory: $this->psrFactory,
            restaurantRepository: $this->restaurantRepository,
            next: null
        );
        $this->next->expects($this->never())->method('handleRequest');
        $this->identity->expects($this->never())->method('isAuthenticated');
        $this->identity->expects($this->never())->method('username');
        $this->context->setIdentity($this->identity);
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $request = $this->configureRequest('/');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No middleware to handle the request');

        $middleware->handleRequest($request);
    }

    public function testHandleRequestPassesThroughWhenIdentityNotAuthenticated(): void
    {
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(false);
        $this->context->setIdentity($this->identity);
        $request = $this->configureRequest('/');
        $expectedResponse = $this->configureNextMiddlewareResponse($request);

        $response = $this->middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testHandleRequestPassesThroughWhenPathIsRestaurantSelectionUrl(): void
    {
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->identity->expects($this->never())->method('username');
        $this->context->setIdentity($this->identity);
        $request = $this->configureRequest($this->settings->selectionPath);
        $expectedResponse = $this->configureNextMiddlewareResponse($request);

        $response = $this->middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testHandleRequestSetsRestaurantIdInContextWhenCookieIsValid(): void
    {
        $restaurantId = $this->faker->uuid;
        $username = $this->faker->email;
        $restaurant = Restaurant::new($username, $restaurantId);
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->identity->expects($this->once())->method('username')->willReturn($username);
        $this->context->setIdentity($this->identity);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($username)
            ->willReturn([$restaurant]);
        $request = $this->configureRequest('/dashboard', [$this->settings->cookieName => $restaurantId]);
        $expectedResponse = $this->configureNextMiddlewareResponse($request);

        $response = $this->middleware->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
        $this->assertSame($restaurantId, $this->context->get($this->settings->contextKey));
    }

    public function testHandleRequestRedirectsWhenCookieIsInvalid(): void
    {
        $email = $this->faker->email;
        $restaurant = Restaurant::new($email);
        $this->next->expects($this->never())->method('handleRequest');
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->identity->expects($this->once())->method('username')->willReturn($email);
        $this->context->setIdentity($this->identity);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($email)
            ->willReturn([$restaurant]);
        $request = $this->configureRequest('/dashboard', [$this->settings->cookieName => $this->faker->uuid]);

        $response = $this->middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        /** @var non-empty-string $selectionPath */
        $selectionPath = $this->settings->selectionPath;
        $this->assertStringStartsWith($selectionPath, $response->getHeaderLine('Location'));
    }

    public function testHandleRequestRedirectsWhenCookieIsMissing(): void
    {
        $this->next->expects($this->never())->method('handleRequest');
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->identity->expects($this->never())->method('username');
        $this->context->setIdentity($this->identity);
        $request = $this->configureRequest('/dashboard', []);

        $response = $this->middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        /** @var non-empty-string $selectionPath */
        $selectionPath = $this->settings->selectionPath;
        $this->assertStringStartsWith($selectionPath, $response->getHeaderLine('Location'));
    }

    public function testHandleRequestRedirectsWithCorrectBackUrl(): void
    {
        $backUrl = $this->faker->url;
        $this->next->expects($this->never())->method('handleRequest');
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->identity->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->identity->expects($this->never())->method('username');
        $this->context->setIdentity($this->identity);
        $request = $this->configureRequest($backUrl, []);

        $response = $this->middleware->handleRequest($request);

        $this->assertEquals(303, $response->getStatusCode());
        $location = $response->getHeaderLine('Location');
        /** @var non-empty-string $path */
        $path = $this->settings->selectionPath;
        $this->assertStringStartsWith($path, $location);
        $this->assertStringContainsString('backUrl=', $location);
        $this->assertStringContainsString(urlencode($backUrl), $location);
    }

    /**
     * @param array<string, string> $cookies
     */
    private function configureRequest(string $path, array $cookies = []): ServerRequest
    {
        $request = new ServerRequest('GET', $path);
        return $request
            ->withAttribute(RequestContext::class, $this->context)
            ->withCookieParams($cookies);
    }

    private function configureNextMiddlewareResponse(ServerRequest $request): ResponseInterface
    {
        $response = $this->psrFactory->createResponse(200);
        $this->next
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
            ->willReturn($response);
        return $response;
    }
}
