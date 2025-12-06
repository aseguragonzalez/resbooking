<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Projects\AddPlace\AddPlace;
use Application\Projects\AddPlace\AddPlaceCommand;
use Application\Projects\RemovePlace\RemovePlace;
use Application\Projects\RemovePlace\RemovePlaceCommand;
use Application\Projects\UpdatePlace\UpdatePlace;
use Application\Projects\UpdatePlace\UpdatePlaceCommand;
use Domain\Projects\ProjectRepository;
use Infrastructure\Ports\Dashboard\Models\Places\Pages\EditPlace;
use Infrastructure\Ports\Dashboard\Models\Places\Pages\PlacesList;
use Infrastructure\Ports\Dashboard\Models\Places\Place;
use Infrastructure\Ports\Dashboard\Models\Places\Requests\AddPlaceRequest;
use Infrastructure\Ports\Dashboard\Models\Places\Requests\UpdatePlaceRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class PlacesController extends Controller
{
    private const string PROJECT_ID = '69347ea320d5a';

    public function __construct(
        private readonly AddPlace $addPlace,
        private readonly RemovePlace $removePlace,
        private readonly UpdatePlace $updatePlace,
        private readonly ProjectRepository $projectRepository,
    ) {
    }

    public function index(): ActionResponse
    {
        $project = $this->projectRepository->getById(self::PROJECT_ID);
        $places = array_map(
            fn ($place) => new Place(
                id: $place->getId(),
                name: $place->name,
                capacity: $place->capacity->value
            ),
            $project->getPlaces()
        );
        $model = PlacesList::create(places: $places);
        return $this->view(model: $model);
    }

    public function create(ServerRequestInterface $request): ActionResponse
    {
        $backUrl = $request->getHeaderLine('Referer') ?: '/places';
        $model = EditPlace::new(backUrl: $backUrl);
        return $this->view('edit', model: $model);
    }

    public function store(AddPlaceRequest $request, ServerRequestInterface $serverRequest): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $serverRequest->getHeaderLine('Referer') ?: '/places';
            $pageModel = EditPlace::withErrors($request, $errors, placeId: null, backUrl: $backUrl);
            return $this->view('edit', model: $pageModel);
        }

        $this->addPlace->execute(new AddPlaceCommand(
            projectId: self::PROJECT_ID,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', PlacesController::class);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $project = $this->projectRepository->getById(self::PROJECT_ID);
        $places = $project->getPlaces();
        $place = array_filter($places, fn ($p) => $p->getId() === $id);
        if (empty($place)) {
            return $this->redirectToAction('index', PlacesController::class);
        }

        $place = array_values($place)[0];
        $backUrl = $request->getHeaderLine('Referer') ?: '/places';
        $model = EditPlace::fromPlace(
            placeId: $place->getId(),
            name: $place->name,
            capacity: $place->capacity->value,
            backUrl: $backUrl
        );
        return $this->view(model: $model);
    }

    public function update(
        string $id,
        UpdatePlaceRequest $request,
        ServerRequestInterface $serverRequest
    ): ActionResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $serverRequest->getHeaderLine('Referer') ?: '/places';
            $pageModel = EditPlace::withErrors($request, $errors, placeId: $id, backUrl: $backUrl);
            return $this->view('edit', model: $pageModel);
        }

        $this->updatePlace->execute(new UpdatePlaceCommand(
            projectId: self::PROJECT_ID,
            placeId: $id,
            name: $request->name,
            capacity: $request->capacity
        ));

        return $this->redirectToAction('index', PlacesController::class);
    }

    public function delete(string $id): ActionResponse
    {
        $this->removePlace->execute(new RemovePlaceCommand(
            projectId: self::PROJECT_ID,
            placeId: $id
        ));

        return $this->redirectToAction('index', PlacesController::class);
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/places'),
                controller: PlacesController::class,
                action: 'index',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/places/create'),
                controller: PlacesController::class,
                action: 'create',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/places'),
                controller: PlacesController::class,
                action: 'store',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/places/{id}'),
                controller: PlacesController::class,
                action: 'edit',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/places/{id}'),
                controller: PlacesController::class,
                action: 'update',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/places/{id}/delete'),
                controller: PlacesController::class,
                action: 'delete',
                authRequired: true
            ),
        ];
    }
}
