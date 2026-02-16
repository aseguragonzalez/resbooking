<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilities;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Faker\Factory;
use Faker\Generator;
use Framework\Mvc\Actions\Responses\LocalRedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Infrastructure\Ports\Dashboard\Controllers\AvailabilitiesController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Pages\AvailabilitiesList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\RestaurantBuilder;

final class AvailabilitiesControllerTest extends TestCase
{
    private GetRestaurantById&MockObject $getRestaurantById;
    private UpdateAvailabilities&MockObject $updateAvailabilities;
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private AvailabilitiesController $controller;
    private Generator $faker;
    private ServerRequestInterface&MockObject $serverRequest;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->getRestaurantById = $this->createMock(GetRestaurantById::class);
        $this->updateAvailabilities = $this->createMock(UpdateAvailabilities::class);
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->settings = new RestaurantContextSettings();
        $this->controller = new AvailabilitiesController(
            $this->getRestaurantById,
            $this->updateAvailabilities,
            $this->settings,
            $this->requestContext,
        );
        $this->faker = Factory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testAvailabilitiesReturnsAvailabilitiesList(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $this->requestContext->set('restaurantId', $restaurant->getId());
        $this->updateAvailabilities->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getParsedBody');
        $this->getRestaurantById
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdQuery $query) use ($restaurant) {
                return $query->id === $restaurant->getId();
            }))
            ->willReturn($restaurant);

        $response = $this->controller->availabilities();

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Availabilities/availabilities', $view->viewPath);
        $this->assertInstanceOf(AvailabilitiesList::class, $view->data);
        /** @var AvailabilitiesList $page */
        $page = $view->data;
        $this->assertCount(count($restaurant->getAvailabilities()), $page->availabilities);
    }

    public function testUpdateAvailabilitiesSuccess(): void
    {
        $parsedBody = [
            '1_2' => 20,  // timeSlotId_dayOfWeekId => capacity
            '2_3' => 15,
        ];
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($parsedBody);
        $this->updateAvailabilities
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateAvailabilitiesCommand $command) {
                return $command->restaurantId === $this->requestContext->get('restaurantId')
                    && count($command->availabilities) === 2;
            }));

        $response = $this->controller->updateAvailabilities($this->serverRequest);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('availabilities', $redirect->action);
        $this->assertEquals(AvailabilitiesController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testGetRoutesConfiguration(): void
    {
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->updateAvailabilities->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getParsedBody');
        $routes = AvailabilitiesController::getRoutes();

        $this->assertCount(2, $routes);

        $getRoute = $routes[0];
        $this->assertEquals(RouteMethod::Get->name, $getRoute->method->name);
        $this->assertEquals('/availabilities', $getRoute->path->value());
        $this->assertEquals(AvailabilitiesController::class, $getRoute->controller);
        $this->assertEquals('availabilities', $getRoute->action);

        $postRoute = $routes[1];
        $this->assertEquals(RouteMethod::Post->name, $postRoute->method->name);
        $this->assertEquals('/availabilities', $postRoute->path->value());
        $this->assertEquals(AvailabilitiesController::class, $postRoute->controller);
        $this->assertEquals('updateAvailabilities', $postRoute->action);
    }
}
