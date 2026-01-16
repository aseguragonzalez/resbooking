<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\AddDiningArea\AddDiningArea;
use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\RemoveDiningArea\RemoveDiningArea;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningArea;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Ports\Dashboard\Controllers\DiningAreasController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\DiningAreasList;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\EditDiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Mvc\Actions\Responses\LocalRedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;

final class DiningAreasControllerTest extends TestCase
{
    private AddDiningArea&MockObject $addDiningArea;
    private RemoveDiningArea&MockObject $removeDiningArea;
    private UpdateDiningArea&MockObject $updateDiningArea;
    private GetRestaurantById&MockObject $getRestaurantById;
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private DiningAreasController $controller;
    private Generator $faker;
    private string $restaurantId;
    private ServerRequestInterface&MockObject $serverRequest;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->restaurantId = uniqid();
        $this->requestContext->set('restaurantId', $this->restaurantId);
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->addDiningArea = $this->createMock(AddDiningArea::class);
        $this->removeDiningArea = $this->createMock(RemoveDiningArea::class);
        $this->updateDiningArea = $this->createMock(UpdateDiningArea::class);
        $this->getRestaurantById = $this->createMock(GetRestaurantById::class);
        $this->settings = new RestaurantContextSettings();
        $this->controller = new DiningAreasController(
            $this->addDiningArea,
            $this->removeDiningArea,
            $this->updateDiningArea,
            $this->getRestaurantById,
            $this->requestContext,
            $this->settings,
        );
        $this->faker = Factory::create();
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
    }

    public function testIndexReturnsDiningAreasList(): void
    {
        $diningArea1 = DiningArea::new(
            capacity: new Capacity(20),
            name: 'Area 1'
        );
        $diningArea2 = DiningArea::new(
            capacity: new Capacity(15),
            name: 'Area 2'
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
            diningAreas: [$diningArea1, $diningArea2]
        );

        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdCommand $command) {
                return $command->id === $this->restaurantId;
            }))
            ->willReturn($restaurant);

        $response = $this->controller->index();

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('DiningAreas/index', $view->viewPath);
        $this->assertInstanceOf(DiningAreasList::class, $view->data);
        /** @var DiningAreasList $page */
        $page = $view->data;
        $this->assertCount(2, $page->diningAreas);
        $this->assertTrue($page->hasDiningAreas);
    }

    public function testCreateReturnsEditView(): void
    {
        $backUrl = '/dining-areas';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $response = $this->controller->create($this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('DiningAreas/edit', $view->viewPath);
        $this->assertInstanceOf(EditDiningArea::class, $view->data);
        /** @var EditDiningArea $page */
        $page = $view->data;
        $this->assertNull($page->diningAreaId);
        $this->assertEquals($backUrl, $page->backUrl);
    }

    public function testStoreWithValidationErrors(): void
    {
        $request = new AddDiningAreaRequest(name: '', capacity: 0);
        $backUrl = '/dining-areas';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $response = $this->controller->store($request, $this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('DiningAreas/edit', $view->viewPath);
        $this->assertInstanceOf(EditDiningArea::class, $view->data);
        /** @var EditDiningArea $page */
        $page = $view->data;
        $this->assertNotEmpty($page->errorSummary);
    }

    public function testStoreSuccess(): void
    {
        $request = new AddDiningAreaRequest(name: 'New Area', capacity: 20);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('/dining-areas');

        $this->addDiningArea->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (AddDiningAreaCommand $command) use ($request) {
                return $command->restaurantId === $this->restaurantId
                    && $command->name === $request->name
                    && $command->capacity === $request->capacity;
            }));

        $response = $this->controller->store($request, $this->serverRequest);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('index', $redirect->action);
        $this->assertEquals(DiningAreasController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testEditReturnsEditView(): void
    {
        $diningAreaId = uniqid();
        $diningArea = DiningArea::build(
            id: $diningAreaId,
            capacity: new Capacity(20),
            name: 'Test Area'
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
            diningAreas: [$diningArea]
        );

        $backUrl = '/dining-areas';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->willReturn($restaurant);

        $response = $this->controller->edit($diningAreaId, $this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('DiningAreas/edit', $view->viewPath);
        $this->assertInstanceOf(EditDiningArea::class, $view->data);
        /** @var EditDiningArea $page */
        $page = $view->data;
        $this->assertEquals($diningAreaId, $page->diningAreaId);
        $this->assertEquals($backUrl, $page->backUrl);
    }

    public function testEditWithNonExistentDiningArea(): void
    {
        $diningAreaId = uniqid();
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
            diningAreas: []
        );

        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->willReturn($restaurant);

        $response = $this->controller->edit($diningAreaId, $this->serverRequest);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('index', $redirect->action);
        $this->assertEquals(DiningAreasController::class, $redirect->controller);
    }

    public function testUpdateWithValidationErrors(): void
    {
        $diningAreaId = uniqid();
        $request = new UpdateDiningAreaRequest(name: '', capacity: 0);
        $backUrl = '/dining-areas';
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

        $response = $this->controller->update($diningAreaId, $request, $this->serverRequest);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('DiningAreas/edit', $view->viewPath);
        $this->assertInstanceOf(EditDiningArea::class, $view->data);
        /** @var EditDiningArea $page */
        $page = $view->data;
        $this->assertNotEmpty($page->errorSummary);
        $this->assertEquals($diningAreaId, $page->diningAreaId);
    }

    public function testUpdateSuccess(): void
    {
        $diningAreaId = uniqid();
        $request = new UpdateDiningAreaRequest(name: 'Updated Area', capacity: 25);
        $this->serverRequest->method('getHeaderLine')->with('Referer')->willReturn('/dining-areas');

        $this->updateDiningArea->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateDiningAreaCommand $command) use ($request, $diningAreaId) {
                return $command->restaurantId === $this->restaurantId
                    && $command->diningAreaId === $diningAreaId
                    && $command->name === $request->name
                    && $command->capacity === $request->capacity;
            }));

        $response = $this->controller->update($diningAreaId, $request, $this->serverRequest);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('index', $redirect->action);
        $this->assertEquals(DiningAreasController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testDeleteSuccess(): void
    {
        $diningAreaId = uniqid();

        $this->removeDiningArea->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (RemoveDiningAreaCommand $command) use ($diningAreaId) {
                return $command->restaurantId === $this->restaurantId
                    && $command->diningAreaId === $diningAreaId;
            }));

        $response = $this->controller->delete($diningAreaId);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('index', $redirect->action);
        $this->assertEquals(DiningAreasController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testGetRoutesConfiguration(): void
    {
        $routes = DiningAreasController::getRoutes();

        $this->assertCount(6, $routes);

        $routesData = [
            ['Get', '/dining-areas', 'index', true],
            ['Get', '/dining-areas/create', 'create', true],
            ['Post', '/dining-areas', 'store', true],
            ['Get', '/dining-areas/{id}', 'edit', true],
            ['Post', '/dining-areas/{id}', 'update', true],
            ['Post', '/dining-areas/{id}/delete', 'delete', true],
        ];

        foreach ($routesData as $index => [$method, $path, $action, $authRequired]) {
            $route = $routes[$index];
            $this->assertEquals($method, $route->method->name);
            $this->assertEquals($path, $route->path->value());
            $this->assertEquals(DiningAreasController::class, $route->controller);
            $this->assertEquals($action, $route->action);
        }
    }
}
