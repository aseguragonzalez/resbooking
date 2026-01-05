<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilities;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Ports\Dashboard\Controllers\AvailabilitiesController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Pages\AvailabilitiesList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\LocalRedirectTo;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;

final class AvailabilitiesControllerTest extends TestCase
{
    private GetRestaurantById&MockObject $getRestaurantById;
    private UpdateAvailabilities&MockObject $updateAvailabilities;
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private AvailabilitiesController $controller;
    private Generator $faker;
    private string $restaurantId;
    private ServerRequestInterface&MockObject $serverRequest;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->restaurantId = uniqid();
        $this->requestContext->set('restaurantId', $this->restaurantId);
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->getRestaurantById = $this->createMock(GetRestaurantById::class);
        $this->updateAvailabilities = $this->createMock(UpdateAvailabilities::class);
        $this->settings = new RestaurantContextSettings();
        $this->controller = new AvailabilitiesController(
            $this->getRestaurantById,
            $this->updateAvailabilities,
            $this->settings,
            $this->requestContext,
        );
        $this->faker = Factory::create();
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
    }

    public function testAvailabilitiesReturnsAvailabilitiesList(): void
    {
        $availability1 = new Availability(
            dayOfWeek: DayOfWeek::Monday,
            capacity: new Capacity(20),
            timeSlot: TimeSlot::H1200
        );
        $availability2 = new Availability(
            dayOfWeek: DayOfWeek::Tuesday,
            capacity: new Capacity(15),
            timeSlot: TimeSlot::H1300
        );

        $restaurant = Restaurant::build(
            id: $this->restaurantId,
            settings: new Settings(
                email: new Email($this->faker->email()),
                hasReminders: true,
                name: 'Test Restaurant',
                maxNumberOfDiners: new Capacity(10),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(20),
                phone: new Phone('+34-555-0100'),
            ),
            availabilities: [$availability1, $availability2]
        );

        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdCommand $command) {
                return $command->id === $this->restaurantId;
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
        $this->assertCount(2, $page->availabilities);
    }

    public function testUpdateAvailabilitiesSuccess(): void
    {
        $parsedBody = [
            '1_2' => 20,  // timeSlotId_dayOfWeekId => capacity
            '2_3' => 15,
        ];
        $this->serverRequest->method('getParsedBody')->willReturn($parsedBody);

        $this->updateAvailabilities->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateAvailabilitiesCommand $command) {
                return $command->restaurantId === $this->restaurantId
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
