<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Ports\Dashboard\Controllers\RestaurantsController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Pages\SelectRestaurant;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Requests\SelectRestaurantRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Mvc\Actions\Responses\RedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;

final class RestaurantsControllerTest extends TestCase
{
    private RestaurantRepository&MockObject $restaurantRepository;
    private RestaurantContextSettings $settings;
    private RestaurantsController $controller;
    private Generator $faker;
    private ServerRequestInterface&MockObject $serverRequest;
    private RequestContext $requestContext;

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
        $this->serverRequest->method('getAttribute')
            ->with(RequestContext::class)
            ->willReturn($this->requestContext);
    }

    public function testSelectWithNoRestaurants(): void
    {
        $userEmail = $this->faker->email();
        $identity = UserIdentity::new($userEmail, ['admin'], 'password')->activate();
        $this->requestContext->setIdentity($identity);

        $backUrl = 'http://localhost/dashboard';
        $this->serverRequest->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('');

        $this->restaurantRepository->expects($this->once())
            ->method('findByUserEmail')
            ->with($userEmail)
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
        $userEmail = $this->faker->email();
        $identity = UserIdentity::new($userEmail, ['admin'], 'password')->activate();
        $this->requestContext->setIdentity($identity);

        $restaurantId = uniqid();
        $restaurantName = $this->faker->company();
        $restaurant = Restaurant::build(
            id: $restaurantId,
            settings: new Settings(
                email: new Email($userEmail),
                hasReminders: true,
                name: $restaurantName,
                maxNumberOfDiners: new Capacity(10),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(20),
                phone: new Phone('+34-555-0100'),
            )
        );

        $backUrl = 'http://localhost/dashboard';
        $this->serverRequest->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('');

        $this->restaurantRepository->expects($this->once())
            ->method('findByUserEmail')
            ->with($userEmail)
            ->willReturn([$restaurant]);

        $response = $this->controller->select($this->serverRequest);

        $this->assertInstanceOf(RedirectTo::class, $response);
        /** @var RedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals($backUrl, $redirect->url);
        $this->assertEquals(302, $response->statusCode->value);

        $setCookieHeaders = array_filter(
            $response->headers,
            fn ($header) => $header instanceof SetCookie
                && str_contains($header->value, $this->settings->cookieName)
        );
        $this->assertCount(1, $setCookieHeaders);
        /** @var SetCookie $setCookie */
        $setCookie = reset($setCookieHeaders);
        $this->assertStringContainsString(
            $this->settings->cookieName . '=' . $restaurantId,
            $setCookie->value
        );
    }

    public function testSelectWithMultipleRestaurants(): void
    {
        $userEmail = $this->faker->email();
        $identity = UserIdentity::new($userEmail, ['admin'], 'password')->activate();
        $this->requestContext->setIdentity($identity);

        $restaurant1 = Restaurant::build(
            id: uniqid(),
            settings: new Settings(
                email: new Email($userEmail),
                hasReminders: true,
                name: 'Restaurant 1',
                maxNumberOfDiners: new Capacity(10),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(20),
                phone: new Phone('+34-555-0100'),
            )
        );
        $restaurant2 = Restaurant::build(
            id: uniqid(),
            settings: new Settings(
                email: new Email($userEmail),
                hasReminders: true,
                name: 'Restaurant 2',
                maxNumberOfDiners: new Capacity(10),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(20),
                phone: new Phone('+34-555-0100'),
            )
        );

        $backUrl = 'http://localhost/dashboard';
        $this->serverRequest->method('getQueryParams')->willReturn(['backUrl' => $backUrl]);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('');

        $this->restaurantRepository->expects($this->once())
            ->method('findByUserEmail')
            ->with($userEmail)
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
        $request = new SelectRestaurantRequest(restaurantId: '', backUrl: 'http://localhost/dashboard');
        $this->serverRequest->method('getQueryParams')->willReturn([]);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('http://localhost/dashboard');

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
        $restaurantId = uniqid();
        $backUrl = 'http://localhost/dashboard';
        $request = new SelectRestaurantRequest(restaurantId: $restaurantId, backUrl: $backUrl);

        $response = $this->controller->setRestaurant($request, $this->serverRequest);

        $this->assertInstanceOf(RedirectTo::class, $response);
        /** @var RedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals($backUrl, $redirect->url);
        $this->assertEquals(302, $response->statusCode->value);
    }

    public function testSetRestaurantSetsCookie(): void
    {
        $restaurantId = uniqid();
        $backUrl = 'http://localhost/dashboard';
        $request = new SelectRestaurantRequest(restaurantId: $restaurantId, backUrl: $backUrl);
        $this->serverRequest->method('getQueryParams')->willReturn([]);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('');

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
            $this->settings->cookieName . '=' . $restaurantId,
            $setCookie->value
        );
    }

    public function testGetRoutesConfiguration(): void
    {
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
