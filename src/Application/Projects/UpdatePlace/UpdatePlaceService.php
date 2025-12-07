<?php

declare(strict_types=1);

namespace Application\Projects\UpdatePlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Domain\Shared\Capacity;

final readonly class UpdatePlaceService implements UpdatePlace
{
    public function __construct(
        private ProjectObtainer $projectObtainer,
        private ProjectRepository $projectRepository,
    ) {
    }

    public function execute(UpdatePlaceCommand $command): void
    {
        $project = $this->projectObtainer->obtain(id: $command->projectId);

        $project->removePlaces(fn (Place $place) => $place->getId() === $command->placeId);

        $updatedPlace = Place::build(
            id: $command->placeId,
            capacity: new Capacity(value: $command->capacity),
            name: $command->name
        );

        $project->addPlace(place: $updatedPlace);
        $this->projectRepository->save($project);
    }
}
