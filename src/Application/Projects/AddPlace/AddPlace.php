<?php

declare(strict_types=1);

namespace App\Application\Projects\AddPlace;

use App\Application\Projects\AddPlace\AddPlaceRequest;
use App\Domain\Projects\Entities\{Project, Place};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\Capacity;
use App\Seedwork\Application\{UseCase, UseCaseRequest};

/**
 * @extends UseCase<AddPlaceRequest>
 */
final class AddPlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute($request): void
    {
        $project = $this->projectRepository->getById(id: $request->projectId);
        $place = Place::new(capacity: new Capacity(value: $request->capacity), name: $request->name);
        $project->addPlace(place: $place);
        $this->projectRepository->save($project);
    }
}
