<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilities;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Pages\AvailabilitiesList;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Requests\UpdateAvailabilitiesRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class AvailabilitiesController extends Controller
{
    private const string RESTAURANT_ID = '69347ea320d5a';

    public function __construct(
        private readonly GetRestaurantById $getRestaurantById,
        private readonly UpdateAvailabilities $updateAvailabilities,
    ) {
    }

    public function availabilities(): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: self::RESTAURANT_ID);
        $restaurant = $this->getRestaurantById->execute($command);
        $availabilities = $restaurant->getAvailabilities();
        $pageModel = AvailabilitiesList::create(availabilities: $availabilities);
        return $this->view(model: $pageModel);
    }

    public function updateAvailabilities(ServerRequestInterface $request): ActionResponse
    {
        $availabilityRequest = new UpdateAvailabilitiesRequest($request);
        $command = new UpdateAvailabilitiesCommand(
            restaurantId: self::RESTAURANT_ID,
            availabilities: $availabilityRequest->availabilities
        );
        $this->updateAvailabilities->execute($command);
        return $this->redirectToAction(action: 'availabilities');
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/availabilities'),
                controller: AvailabilitiesController::class,
                action: 'availabilities',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/availabilities'),
                controller: AvailabilitiesController::class,
                action: 'updateAvailabilities',
                authRequired: true
            ),
        ];
    }
}
