<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\UpdateSettings\UpdateSettings;
use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class SettingsControllerTest extends TestCase
{
    private UpdateSettings&MockObject $updateSettings;
    private GetRestaurantById&MockObject $getRestaurantById;
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private SettingsController $controller;
    private Generator $faker;
    private string $restaurantId;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->restaurantId = uniqid();
        $this->requestContext->set('restaurantId', $this->restaurantId);
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
    }

    public function testSettingsReturnsUpdateSettingsPage(): void
    {
        $email = $this->faker->email();
        $name = $this->faker->company();
        $phone = $this->faker->phoneNumber();
        $hasReminders = true;
        $maxNumberOfDiners = 10;
        $minNumberOfDiners = 1;
        $numberOfTables = 20;

        $restaurantSettings = new Settings(
            email: new Email($email),
            hasReminders: $hasReminders,
            name: $name,
            maxNumberOfDiners: new Capacity($maxNumberOfDiners),
            minNumberOfDiners: new Capacity($minNumberOfDiners),
            numberOfTables: new Capacity($numberOfTables),
            phone: new Phone($phone),
        );

        $restaurant = Restaurant::build(
            id: $this->restaurantId,
            settings: $restaurantSettings
        );

        $this->getRestaurantById->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetRestaurantByIdCommand $command) {
                return $command->id === $this->restaurantId;
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
        $request = new UpdateSettingsRequest(
            email: $this->faker->email(),
            name: $this->faker->company(),
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 20,
            phone: $this->faker->phoneNumber(),
            hasReminders: 'on',
        );

        $this->updateSettings->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateSettingsCommand $command) use ($request) {
                return $command->restaurantId === $this->restaurantId
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
