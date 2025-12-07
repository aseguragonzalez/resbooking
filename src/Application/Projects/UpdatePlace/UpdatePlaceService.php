<?php

declare(strict_types=1);

namespace Application\Projects\UpdatePlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Shared\Capacity;

final readonly class UpdatePlaceService implements UpdatePlace
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function execute(UpdatePlaceCommand $command): void
    {
        $project = $this->projectRepository->getById($command->projectId);

        // Remove the old place
        $project->removePlaces(fn (Place $place) => $place->getId() === $command->placeId);

        // Create new place with the same ID and updated values
        $updatedPlace = Place::build(
            id: $command->placeId,
            capacity: new Capacity(value: $command->capacity),
            name: $command->name
        );

        // Add the updated place
        $project->addPlace(place: $updatedPlace);
        $this->projectRepository->save($project);
    }
}
