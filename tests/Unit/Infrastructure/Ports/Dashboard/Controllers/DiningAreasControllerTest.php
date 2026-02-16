<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\AddDiningArea\AddDiningArea;
use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\RemoveDiningArea\RemoveDiningArea;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningArea;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Faker\Factory;
use Faker\Generator;
use Framework\Mvc\Actions\Responses\LocalRedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Infrastructure\Ports\Dashboard\Controllers\DiningAreasController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\DiningAreasList;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\EditDiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\RestaurantBuilder;

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
    private ServerRequestInterface&MockObject $serverRequest;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
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
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testIndexReturnsDiningAreasList(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $this->requestContext->set('restaurantId', $restaurant->getId());
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->getRestaurantById
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdQuery $query) use ($restaurant) {
                return $query->id === $restaurant->getId();
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
        $this->assertCount(count($restaurant->getDiningAreas()), $page->diningAreas);
        $this->assertTrue($page->hasDiningAreas);
    }

    public function testCreateReturnsEditView(): void
    {
        $backUrl = '/dining-areas';
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->once())->method('getHeaderLine')->with('Referer')->willReturn($backUrl);

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
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->serverRequest->expects($this->once())->method('getHeaderLine')->with('Referer')->willReturn($backUrl);
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');

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
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->addDiningArea->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (AddDiningAreaCommand $command) use ($request) {
                return $command->restaurantId === $this->requestContext->get('restaurantId')
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
        $restaurant = $this->restaurantBuilder->build();
        $diningAreaId = $restaurant->getDiningAreas()[0]->id;
        $backUrl = '/dining-areas';
        $this->requestContext->set('restaurantId', $restaurant->getId());
        $this->serverRequest->expects($this->once())->method('getHeaderLine')->with('Referer')->willReturn($backUrl);
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdQuery $query) use ($restaurant) {
                return $query->id === $restaurant->getId();
            }))
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
        $diningAreaId = $this->faker->uuid();
        $restaurant = $this->restaurantBuilder->build();
        $this->requestContext->set('restaurantId', $restaurant->getId());
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdQuery $query) use ($restaurant) {
                return $query->id === $restaurant->getId();
            }))
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
        $diningAreaId = $this->faker->uuid();
        $request = new UpdateDiningAreaRequest(name: '', capacity: 0);
        $backUrl = '/dining-areas';
        $this->serverRequest->expects($this->once())->method('getHeaderLine')->with('Referer')->willReturn($backUrl);
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');

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
        $diningAreaId = $this->faker->uuid();
        $request = new UpdateDiningAreaRequest(name: 'Updated Area', capacity: 25);
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->removeDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->updateDiningArea
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateDiningAreaCommand $command) use ($request, $diningAreaId) {
                return $command->restaurantId === $this->requestContext->get('restaurantId')
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
        $diningAreaId = $this->faker->uuid();
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->removeDiningArea
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (RemoveDiningAreaCommand $command) use ($diningAreaId) {
                return $command->restaurantId === $this->requestContext->get('restaurantId')
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
        $this->addDiningArea->expects($this->never())->method('execute');
        $this->updateDiningArea->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->serverRequest->expects($this->never())->method('getHeaderLine');
        $this->removeDiningArea->expects($this->never())->method('execute');

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
