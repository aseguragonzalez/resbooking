<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Domain\Shared\Capacity;

final readonly class AddPlaceService implements AddPlace
{
    public function __construct(
        private ProjectObtainer $projectObtainer,
        private ProjectRepository $projectRepository,
    ) {
    }

    public function execute(AddPlaceCommand $command): void
    {
        $project = $this->projectObtainer->obtain(id: $command->projectId);
        $place = Place::new(capacity: new Capacity(value: $command->capacity), name: $command->name);
        $project->addPlace(place: $place);
        $this->projectRepository->save($project);
    }
}
