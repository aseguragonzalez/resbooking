<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Faker\Factory;
use Faker\Generator;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Reservations\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateReservationRequest;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateStatusRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\LocalRedirectTo;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;

final class ReservationsControllerTest extends TestCase
{
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private ReservationsController $controller;
    private Generator $faker;
    private string $restaurantId;
    private ServerRequestInterface&MockObject $serverRequest;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->restaurantId = uniqid();
        $this->requestContext->set('restaurantId', $this->restaurantId);
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->settings = new RestaurantContextSettings();
        $this->controller = new ReservationsController(
            $this->requestContext,
            $this->settings,
        );
        $this->faker = Factory::create();
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
    }

    public function testIndexReturnsReservationsList(): void
    {
        $response = $this->controller->index();

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Reservations/index', $view->viewPath);
        $this->assertInstanceOf(Reservations::class, $view->data);
        /** @var Reservations $page */
        $page = $view->data;
        $this->assertCount(10, $page->reservations);
        $this->assertTrue($page->hasReservations);
        $this->assertEquals(0, $page->offset);
        $this->assertEquals(0, $page->prev);
        $this->assertEquals(1, $page->next);
        $this->assertTrue($page->prevDisabled);
    }

    public function testIndexWithCustomParameters(): void
    {
        $offset = 5;
        $from = '2024-01-15';

        $response = $this->controller->index(offset: $offset, from: $from);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(Reservations::class, $view->data);
        /** @var Reservations $page */
        $page = $view->data;
        $this->assertEquals($offset, $page->offset);
        $this->assertEquals('2024-01-15', $page->date);
        $this->assertEquals(4, $page->prev);
        $this->assertEquals(6, $page->next);
    }

    public function testUpdateStatusRedirectsToIndex(): void
    {
        $request = new UpdateStatusRequest(
            id: $this->faker->uuid(),
            status: 'ACCEPTED',
            offset: 3,
            from: '2024-01-20'
        );

        $response = $this->controller->updateStatus($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('index', $redirect->action);
        $this->assertEquals(ReservationsController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
        $this->assertNotNull($redirect->args);
    }

    public function testCreateReturnsEditView(): void
    {
        $backUrl = '/reservations';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $response = $this->controller->create($this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Reservations/edit', $view->viewPath);
        $this->assertObjectHasProperty('pageTitle', $view->data);
        $this->assertObjectHasProperty('reservation', $view->data);
        $this->assertObjectHasProperty('backUrl', $view->data);
    }

    public function testCreateReturnsEditViewWithoutReferer(): void
    {
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('');

        $response = $this->controller->create($this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
    }

    public function testEditReturnsEditView(): void
    {
        $reservationId = $this->faker->uuid();
        $backUrl = '/reservations';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $response = $this->controller->edit($reservationId, $this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Reservations/edit', $view->viewPath);
        $this->assertObjectHasProperty('pageTitle', $view->data);
        $this->assertObjectHasProperty('reservation', $view->data);
        $this->assertObjectHasProperty('backUrl', $view->data);
    }

    public function testUpdateReturnsEditViewWithErrors(): void
    {
        $reservationId = $this->faker->uuid();
        $request = new UpdateReservationRequest(
            id: $reservationId,
            backUrl: '/reservations',
            name: $this->faker->name(),
            email: $this->faker->email(),
            phone: $this->faker->phoneNumber()
        );

        $response = $this->controller->update($reservationId, $request);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Reservations/edit', $view->viewPath);
        $this->assertObjectHasProperty('pageTitle', $view->data);
        $this->assertObjectHasProperty('reservation', $view->data);
        $this->assertObjectHasProperty('backUrl', $view->data);
        $this->assertObjectHasProperty('errors', $view->data);
    }

    public function testGetRoutesConfiguration(): void
    {
        $routes = ReservationsController::getRoutes();

        $this->assertCount(5, $routes);

        $routesData = [
            ['Get', '/reservations', 'index'],
            ['Get', '/reservations/create', 'create'],
            ['Get', '/reservations/{id}', 'edit'],
            ['Post', '/reservations/{id}', 'update'],
            ['Post', '/reservations/{id}/status', 'updateStatus'],
        ];

        foreach ($routesData as $index => [$method, $path, $action]) {
            $route = $routes[$index];
            $this->assertEquals($method, $route->method->name);
            $this->assertEquals($path, $route->path->value());
            $this->assertEquals(ReservationsController::class, $route->controller);
            $this->assertEquals($action, $route->action);
        }
    }
}
