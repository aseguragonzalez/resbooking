<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Framework\Actions\MvcAction;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdResult;
use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Framework\Actions\Responses\ActionResponse;
use Framework\Requests\RequestContext;
use Framework\Routes\Path;
use Framework\Routes\Route;
use Framework\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Settings\Pages\UpdateSettings as UpdateSettingsPage;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;
use SeedWork\Application\CommandBus;
use SeedWork\Application\QueryBus;

final class SettingsController extends RestaurantBaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    #[MvcAction]
    public function settings(): ActionResponse
    {
        $query = new GetRestaurantByIdQuery(id: $this->getRestaurantId());
        /** @var GetRestaurantByIdResult $result */
        $result = $this->queryBus->ask($query);

        $pageModel = UpdateSettingsPage::new(
            email: $result->email,
            hasReminders: $result->hasReminders,
            name: $result->name,
            maxNumberOfDiners: $result->maxNumberOfDiners,
            minNumberOfDiners: $result->minNumberOfDiners,
            numberOfTables: $result->numberOfTables,
            phone: $result->phone,
        );
        return $this->view('Settings/settings', model: $pageModel);
    }

    #[MvcAction]
    public function updateSettings(UpdateSettingsRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $pageModel = UpdateSettingsPage::withErrors($request, $errors);
            return $this->view('Settings/settings', model: $pageModel);
        }

        $this->commandBus->dispatch(new UpdateSettingsCommand(
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
