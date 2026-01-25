<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\UpdateSettings\UpdateSettings;
use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Faker\Factory;
use Faker\Generator;
use Framework\Mvc\Actions\Responses\LocalRedirectTo;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Infrastructure\Ports\Dashboard\Controllers\SettingsController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Settings\Pages\UpdateSettings as UpdateSettingsPage;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class SettingsControllerTest extends TestCase
{
    private UpdateSettings&MockObject $updateSettings;
    private GetRestaurantById&MockObject $getRestaurantById;
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private SettingsController $controller;
    private Generator $faker;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->requestContext->setIdentity(UserIdentity::anonymous());
        $this->updateSettings = $this->createMock(UpdateSettings::class);
        $this->getRestaurantById = $this->createMock(GetRestaurantById::class);
        $this->settings = new RestaurantContextSettings();
        $this->controller = new SettingsController(
            $this->updateSettings,
            $this->getRestaurantById,
            $this->requestContext,
            $this->settings,
        );
        $this->faker = Factory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testSettingsReturnsUpdateSettingsPage(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $this->requestContext->set('restaurantId', $restaurant->getId());

        $this->updateSettings->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdCommand $command) use ($restaurant) {
                return $command->id === $restaurant->getId();
            }))
            ->willReturn($restaurant);

        $response = $this->controller->settings();

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Settings/settings', $view->viewPath);
        $this->assertInstanceOf(UpdateSettingsPage::class, $view->data);
    }

    public function testUpdateSettingsWithValidationErrors(): void
    {
        $this->updateSettings->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $request = new UpdateSettingsRequest(
            email: '',
            name: '',
            maxNumberOfDiners: 0,
            minNumberOfDiners: 0,
            numberOfTables: 0,
            phone: '',
        );

        $response = $this->controller->updateSettings($request);

        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Settings/settings', $view->viewPath);
        $this->assertInstanceOf(UpdateSettingsPage::class, $view->data);
        /** @var UpdateSettingsPage $page */
        $page = $view->data;
        $this->assertNotEmpty($page->errorSummary);
    }

    public function testUpdateSettingsSuccess(): void
    {
        $this->requestContext->set('restaurantId', $this->faker->uuid());
        $request = new UpdateSettingsRequest(
            email: $this->faker->email(),
            name: $this->faker->company(),
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 20,
            phone: $this->faker->phoneNumber(),
            hasReminders: 'on',
        );
        $this->getRestaurantById->expects($this->never())->method('execute');
        $this->updateSettings->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateSettingsCommand $command) use ($request) {
                return !empty($command->restaurantId)
                    && $command->email === $request->email
                    && $command->name === $request->name
                    && $command->hasReminders === true
                    && $command->maxNumberOfDiners === $request->maxNumberOfDiners
                    && $command->minNumberOfDiners === $request->minNumberOfDiners
                    && $command->numberOfTables === $request->numberOfTables
                    && $command->phone === $request->phone;
            }));

        $response = $this->controller->updateSettings($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $redirect */
        $redirect = $response;
        $this->assertEquals('settings', $redirect->action);
        $this->assertEquals(SettingsController::class, $redirect->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testGetRoutesConfiguration(): void
    {
        $this->updateSettings->expects($this->never())->method('execute');
        $this->getRestaurantById->expects($this->never())->method('execute');
        $routes = SettingsController::getRoutes();

        $this->assertCount(2, $routes);

        $getRoute = $routes[0];
        $this->assertEquals(RouteMethod::Get->name, $getRoute->method->name);
        $this->assertEquals('/settings', $getRoute->path->value());
        $this->assertEquals(SettingsController::class, $getRoute->controller);
        $this->assertEquals('settings', $getRoute->action);

        $postRoute = $routes[1];
        $this->assertEquals(RouteMethod::Post->name, $postRoute->method->name);
        $this->assertEquals('/settings', $postRoute->path->value());
        $this->assertEquals(SettingsController::class, $postRoute->controller);
        $this->assertEquals('updateSettings', $postRoute->action);
    }
}
