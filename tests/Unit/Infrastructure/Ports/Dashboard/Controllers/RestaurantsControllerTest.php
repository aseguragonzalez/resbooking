<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Faker\Factory;
use Faker\Generator;
use Framework\Mvc\Actions\Responses\RedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Infrastructure\Ports\Dashboard\Controllers\RestaurantsController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Pages\SelectRestaurant;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Requests\SelectRestaurantRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\RestaurantBuilder;

final class RestaurantsControllerTest extends TestCase
{
    private RestaurantRepository&MockObject $restaurantRepository;
    private RestaurantContextSettings $settings;
    private RestaurantsController $controller;
    private Generator $faker;
    private ServerRequestInterface&MockObject $serverRequest;
    private RequestContext $requestContext;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->settings = new RestaurantContextSettings();
        $this->controller = new RestaurantsController(
            $this->restaurantRepository,
            $this->settings,
        );
        $this->faker = Factory::create();
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testSelectWithNoRestaurants(): void
    {
        $backUrl = $this->faker->url();
        $identity = UserIdentity::new($this->faker->email(), ['admin'], $this->faker->password())->activate();
        $this->requestContext->setIdentity($identity);
        $this->serverRequest->expects($this->once())->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->serverRequest
            ->expects($this->once())
            ->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($this->requestContext);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($identity->username())
            ->willReturn([]);

        $response = $this->controller->select($this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Restaurants/select', $view->viewPath);
        $this->assertInstanceOf(SelectRestaurant::class, $view->data);
        /** @var SelectRestaurant $page */
        $page = $view->data;
        $this->assertTrue($page->hasNoRestaurants);
        $this->assertEquals($backUrl, $page->backUrl);
    }

    public function testSelectWithOneRestaurant(): void
    {
        $backUrl = $this->faker->url();
        $restaurant = $this->restaurantBuilder->build();
        $identity = UserIdentity::new($this->faker->email(), ['admin'], $this->faker->password())->activate();
        $this->requestContext->setIdentity($identity);
        $this->serverRequest->expects($this->once())->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->serverRequest
            ->expects($this->once())
            ->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($this->requestContext);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($identity->username())
            ->willReturn([$restaurant]);

        $response = $this->controller->select($this->serverRequest);

        $this->assertInstanceOf(RedirectTo::class, $response);
        /** @var RedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals($backUrl, $redirect->url);
        $this->assertEquals(302, $response->statusCode->value);
        $setCookieHeaders = array_filter(
            $response->headers,
            fn ($header) => $header instanceof SetCookie && str_contains($header->value, $this->settings->cookieName)
        );
        $this->assertCount(1, $setCookieHeaders);
        /** @var SetCookie $setCookie */
        $setCookie = reset($setCookieHeaders);
        $this->assertStringContainsString(
            $this->settings->cookieName . '=' . $restaurant->getId(),
            $setCookie->value
        );
    }

    public function testSelectWithMultipleRestaurants(): void
    {
        $backUrl = $this->faker->url();
        $restaurant1 = $this->restaurantBuilder->build();
        $restaurant2 = $this->restaurantBuilder->build();
        $identity = UserIdentity::new($this->faker->email(), ['admin'], $this->faker->password())->activate();
        $this->requestContext->setIdentity($identity);
        $this->serverRequest->expects($this->once())->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->serverRequest
            ->expects($this->once())
            ->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($this->requestContext);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('findByUserEmail')
            ->with($identity->username())
            ->willReturn([$restaurant1, $restaurant2]);

        $response = $this->controller->select($this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Restaurants/select', $view->viewPath);
        $this->assertInstanceOf(SelectRestaurant::class, $view->data);
        /** @var SelectRestaurant $page */
        $page = $view->data;
        $this->assertFalse($page->hasNoRestaurants);
        $this->assertCount(2, $page->restaurants);
        $this->assertEquals($backUrl, $page->backUrl);
    }

    public function testSetRestaurantWithValidationErrors(): void
    {
        $backUrl = $this->faker->url();
        $request = new SelectRestaurantRequest(restaurantId: '', backUrl: $backUrl);
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->serverRequest->expects($this->once())->method('getQueryParams')->willReturn([]);
        $this->serverRequest
            ->expects($this->once())
            ->method('getHeaderLine')
            ->with('Referer')
            ->willReturn($backUrl);

        $response = $this->controller->setRestaurant($request, $this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Restaurants/select', $view->viewPath);
        $this->assertInstanceOf(SelectRestaurant::class, $view->data);
        /** @var SelectRestaurant $page */
        $page = $view->data;
        $this->assertNotEmpty($page->errorSummary);
    }

    public function testSetRestaurantSuccess(): void
    {
        $request = new SelectRestaurantRequest(restaurantId: $this->faker->uuid(), backUrl: $this->faker->url());
        $this->serverRequest->expects($this->never())->method('getQueryParams');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $response = $this->controller->setRestaurant($request, $this->serverRequest);

        $this->assertInstanceOf(RedirectTo::class, $response);
        /** @var RedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals($request->backUrl, $redirect->url);
        $this->assertEquals(302, $response->statusCode->value);
    }

    public function testSetRestaurantSetsCookie(): void
    {
        $request = new SelectRestaurantRequest(restaurantId: $this->faker->uuid(), backUrl: $this->faker->url());
        $this->serverRequest->expects($this->never())->method('getQueryParams');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');

        $response = $this->controller->setRestaurant($request, $this->serverRequest);

        $setCookieHeaders = array_filter(
            $response->headers,
            fn ($header) => $header instanceof SetCookie
                && str_contains($header->value, $this->settings->cookieName)
        );
        $this->assertCount(1, $setCookieHeaders);
        /** @var SetCookie $setCookie */
        $setCookie = reset($setCookieHeaders);
        $this->assertStringContainsString(
            $this->settings->cookieName . '=' . $request->restaurantId,
            $setCookie->value
        );
    }

    public function testGetRoutesConfiguration(): void
    {
        $this->restaurantRepository->expects($this->never())->method('findByUserEmail');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->serverRequest->expects($this->never())->method('getAttribute');
        $this->serverRequest->expects($this->never())->method('getQueryParams');

        $routes = RestaurantsController::getRoutes();

        $this->assertCount(2, $routes);

        $getRoute = $routes[0];
        $this->assertEquals(RouteMethod::Get->name, $getRoute->method->name);
        $this->assertEquals('/restaurants/select', $getRoute->path->value());
        $this->assertEquals(RestaurantsController::class, $getRoute->controller);
        $this->assertEquals('select', $getRoute->action);

        $postRoute = $routes[1];
        $this->assertEquals(RouteMethod::Post->name, $postRoute->method->name);
        $this->assertEquals('/restaurants/select', $postRoute->path->value());
        $this->assertEquals(RestaurantsController::class, $postRoute->controller);
        $this->assertEquals('setRestaurant', $postRoute->action);
    }
}
