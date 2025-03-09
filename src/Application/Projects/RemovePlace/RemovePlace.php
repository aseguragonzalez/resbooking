<?php

declare(strict_types=1);

namespace App\Application\Projects\RemovePlace;

use App\Domain\Projects\Entities\Place;
use App\Domain\Projects\ProjectRepository;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<RemovePlaceRequest>
 * @extends UseCase<RemovePlaceRequest>
 */
final class RemovePlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemovePlaceRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $project->removePlaces(fn(Place $place) => $place->getId() === $request->placeId);
        $this->projectRepository->save($project);
    }
}
