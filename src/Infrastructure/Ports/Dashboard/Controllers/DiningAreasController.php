<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\GetRestaurantById\DiningAreaItem;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdResult;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\DiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\DiningAreasList;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\EditDiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use Psr\Http\Message\ServerRequestInterface;
use SeedWork\Application\CommandBus;
use SeedWork\Application\QueryBus;

final class DiningAreasController extends RestaurantBaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    public function index(): ActionResponse
    {
        $query = new GetRestaurantByIdQuery(id: $this->getRestaurantId());
        /** @var GetRestaurantByIdResult $result */
        $result = $this->queryBus->ask($query);
        $diningAreas = array_map(
            fn (DiningAreaItem $da) => new DiningArea(
                id: $da->id,
                name: $da->name,
                capacity: $da->capacity
            ),
            $result->diningAreas
        );
        $model = DiningAreasList::create(diningAreas: $diningAreas);
        return $this->view(model: $model);
    }

    public function create(ServerRequestInterface $request): ActionResponse
    {
        $backUrl = $request->getHeaderLine('Referer') ?: '/dining-areas';
        $model = EditDiningArea::new(backUrl: $backUrl);
        return $this->view('edit', model: $model);
    }

    public function store(AddDiningAreaRequest $request, ServerRequestInterface $serverRequest): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $serverRequest->getHeaderLine('Referer') ?: '/dining-areas';
            $pageModel = EditDiningArea::withErrors($request, $errors, diningAreaId: null, backUrl: $backUrl);
            return $this->view('edit', model: $pageModel);
        }

        $this->commandBus->dispatch(new AddDiningAreaCommand(
            restaurantId: $this->getRestaurantId(),
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $query = new GetRestaurantByIdQuery(id: $this->getRestaurantId());
        /** @var GetRestaurantByIdResult $result */
        $result = $this->queryBus->ask($query);
        $diningArea = null;
        foreach ($result->diningAreas as $da) {
            if ($da->id === $id) {
                $diningArea = $da;
                break;
            }
        }
        if ($diningArea === null) {
            return $this->redirectToAction('index', DiningAreasController::class);
        }

        $backUrl = $request->getHeaderLine('Referer') ?: '/dining-areas';
        $model = EditDiningArea::fromDiningArea(
            diningAreaId: $diningArea->id,
            name: $diningArea->name,
            capacity: $diningArea->capacity,
            backUrl: $backUrl
        );
        return $this->view(model: $model);
    }

    public function update(
        string $id,
        UpdateDiningAreaRequest $request,
        ServerRequestInterface $serverRequest
    ): ActionResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $serverRequest->getHeaderLine('Referer') ?: '/dining-areas';
            $pageModel = EditDiningArea::withErrors($request, $errors, diningAreaId: $id, backUrl: $backUrl);
            return $this->view('edit', model: $pageModel);
        }

        $this->commandBus->dispatch(new UpdateDiningAreaCommand(
            restaurantId: $this->getRestaurantId(),
            diningAreaId: $id,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function delete(string $id): ActionResponse
    {
        $this->commandBus->dispatch(new RemoveDiningAreaCommand(
            restaurantId: $this->getRestaurantId(),
            diningAreaId: $id
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/dining-areas'),
                controller: DiningAreasController::class,
                action: 'index',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/dining-areas/create'),
                controller: DiningAreasController::class,
                action: 'create',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/dining-areas'),
                controller: DiningAreasController::class,
                action: 'store',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/dining-areas/{id}'),
                controller: DiningAreasController::class,
                action: 'edit',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/dining-areas/{id}'),
                controller: DiningAreasController::class,
                action: 'update',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/dining-areas/{id}/delete'),
                controller: DiningAreasController::class,
                action: 'delete',
                authRequired: true
            ),
        ];
    }
}
