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
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\EditDiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\DiningAreasList;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\DiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class DiningAreasController extends Controller
{
    private const string RESTAURANT_ID = '69347ea320d5a';

    public function __construct(
        private readonly AddDiningArea $addDiningArea,
        private readonly RemoveDiningArea $removeDiningArea,
        private readonly UpdateDiningArea $updateDiningArea,
        private readonly GetRestaurantById $getRestaurantById,
    ) {
    }

    public function index(): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: self::RESTAURANT_ID);
        $restaurant = $this->getRestaurantById->execute($command);
        $diningAreas = array_map(
            fn ($diningArea) => new DiningArea(
                id: $diningArea->getId(),
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
            restaurantId: self::RESTAURANT_ID,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $command = new GetRestaurantByIdCommand(id: self::RESTAURANT_ID);
        $restaurant = $this->getRestaurantById->execute($command);
        $diningAreas = $restaurant->getDiningAreas();
        $diningArea = array_filter($diningAreas, fn ($da) => $da->getId() === $id);
        if (empty($diningArea)) {
            return $this->redirectToAction('index', DiningAreasController::class);
        }

        $diningArea = array_values($diningArea)[0];
        $backUrl = $request->getHeaderLine('Referer') ?: '/dining-areas';
        $model = EditDiningArea::fromDiningArea(
            diningAreaId: $diningArea->getId(),
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
            restaurantId: self::RESTAURANT_ID,
            diningAreaId: $id,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', DiningAreasController::class);
    }

    public function delete(string $id): ActionResponse
    {
        $this->removeDiningArea->execute(new RemoveDiningAreaCommand(
            restaurantId: self::RESTAURANT_ID,
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
