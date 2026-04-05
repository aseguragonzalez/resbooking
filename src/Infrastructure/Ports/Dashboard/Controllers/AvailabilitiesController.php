<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Framework\Actions\MvcAction;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdResult;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Framework\Actions\Responses\ActionResponse;
use Framework\Requests\RequestContext;
use Framework\Routes\Path;
use Framework\Routes\Route;
use Framework\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Pages\AvailabilitiesList;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Requests\UpdateAvailabilitiesRequest;
use Psr\Http\Message\ServerRequestInterface;
use SeedWork\Application\CommandBus;
use SeedWork\Application\QueryBus;

final class AvailabilitiesController extends RestaurantBaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        RestaurantContextSettings $settings,
        RequestContext $requestContext,
    ) {
        parent::__construct($requestContext, $settings);
    }

    #[MvcAction]
    public function availabilities(): ActionResponse
    {
        $query = new GetRestaurantByIdQuery(id: $this->getRestaurantId());
        /** @var GetRestaurantByIdResult $result */
        $result = $this->queryBus->ask($query);
        $pageModel = AvailabilitiesList::createFromResultAvailabilities($result->availabilities);
        return $this->view('Availabilities/availabilities', model: $pageModel);
    }

    #[MvcAction]
    public function updateAvailabilities(ServerRequestInterface $request): ActionResponse
    {
        $availabilityRequest = new UpdateAvailabilitiesRequest($request);
        $command = new UpdateAvailabilitiesCommand(
            restaurantId: $this->getRestaurantId(),
            availabilities: $availabilityRequest->availabilities
        );
        $this->commandBus->dispatch($command);
        return $this->redirectToAction('availabilities', AvailabilitiesController::class);
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
