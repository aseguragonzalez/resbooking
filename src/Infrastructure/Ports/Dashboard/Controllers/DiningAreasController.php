<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\AddDiningArea\AddDiningArea;
use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\GetRestaurantById\GetRestaurantById;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\RemoveDiningArea\RemoveDiningArea;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningArea;
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

final class DiningAreasController extends RestaurantBaseController
{
    public function __construct(
        private readonly AddDiningArea $addDiningArea,
        private readonly RemoveDiningArea $removeDiningArea,
        private readonly UpdateDiningArea $updateDiningArea,
        private readonly GetRestaurantById $getRestaurantById,
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

    public function index(): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: $this->getRestaurantId());
        $restaurant = $this->getRestaurantById->execute($command);
        $diningAreas = array_map(
            fn ($diningArea) => new DiningArea(
                id: $diningArea->id,
                name: $diningArea->name,
                capacity: $diningArea->capacity->value
            ),
            $restaurant->getDiningAreas()
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

        $this->addDiningArea->execute(new AddDiningAreaCommand(
            restaurantId: $this->getRestaurantId(),
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: $this->getRestaurantId());
        $restaurant = $this->getRestaurantById->execute($command);
        $diningAreas = $restaurant->getDiningAreas();
        $diningArea = array_filter($diningAreas, fn ($da) => $da->id === $id);
        if (empty($diningArea)) {
            return $this->redirectToAction('index', DiningAreasController::class);
        }

        $diningArea = array_values($diningArea)[0];
        $backUrl = $request->getHeaderLine('Referer') ?: '/dining-areas';
        $model = EditDiningArea::fromDiningArea(
            diningAreaId: $diningArea->id,
            name: $diningArea->name,
            capacity: $diningArea->capacity->value,
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

        $this->updateDiningArea->execute(new UpdateDiningAreaCommand(
            restaurantId: $this->getRestaurantId(),
            diningAreaId: $id,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function delete(string $id): ActionResponse
    {
        $this->removeDiningArea->execute(new RemoveDiningAreaCommand(
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
