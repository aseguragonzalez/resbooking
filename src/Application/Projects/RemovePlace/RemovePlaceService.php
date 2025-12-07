<?php

declare(strict_types=1);

namespace Application\Projects\RemovePlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;

final readonly class RemovePlaceService implements RemovePlace
{
    public function __construct(
        private ProjectObtainer $projectObtainer,
        private ProjectRepository $projectRepository,
    ) {
    }

    public function execute(RemovePlaceCommand $command): void
    {
        $project = $this->projectObtainer->obtain(id: $command->projectId);
        $project->removePlaces(fn (Place $place) => $place->getId() === $command->placeId);
        $this->projectRepository->save($project);
    }
}
