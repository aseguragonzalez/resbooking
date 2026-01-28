<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\UpdateSettings\UpdateSettings;
use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Settings\Pages\UpdateSettings as UpdateSettingsPage;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;

final class SettingsController extends RestaurantBaseController
{
    public function __construct(
        private readonly UpdateSettings $updateSettings,
        private readonly GetRestaurantById $getRestaurantById,
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    public function settings(): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: $this->getRestaurantId());
        $restaurant = $this->getRestaurantById->execute($command);
        $settings = $restaurant->getSettings();

        $pageModel = UpdateSettingsPage::new(
            email: $settings->email->value,
            hasReminders: $settings->hasReminders,
            name: $settings->name,
            maxNumberOfDiners: $settings->maxNumberOfDiners->value,
            minNumberOfDiners: $settings->minNumberOfDiners->value,
            numberOfTables: $settings->numberOfTables->value,
            phone: $settings->phone->value,
        );
        return $this->view(model: $pageModel);
    }

    public function updateSettings(UpdateSettingsRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $pageModel = UpdateSettingsPage::withErrors($request, $errors);
            return $this->view("settings", model: $pageModel);
        }

        $this->updateSettings->execute(new UpdateSettingsCommand(
            restaurantId: $this->getRestaurantId(),
            email: $request->email,
            hasReminders: $request->hasRemindersChecked(),
            name: $request->name,
            maxNumberOfDiners: $request->maxNumberOfDiners,
            minNumberOfDiners: $request->minNumberOfDiners,
            numberOfTables: $request->numberOfTables,
            phone: $request->phone,
        ));
        return $this->redirectToAction("settings", SettingsController::class);
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/settings'),
                controller: SettingsController::class,
                action: 'settings',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/settings'),
                controller: SettingsController::class,
                action: 'updateSettings',
                authRequired: true
            ),
        ];
    }
}
