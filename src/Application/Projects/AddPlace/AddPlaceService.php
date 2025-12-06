<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\ProjectRepository;
use Domain\Shared\Capacity;

final class AddPlaceService implements AddPlace
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddPlaceCommand $command): void
    {
        $project = $this->projectRepository->getById(id: $command->projectId);
        $place = Place::new(capacity: new Capacity(value: $command->capacity), name: $command->name);
        $project->addPlace(place: $place);
        $this->projectRepository->save($project);
    }
}
