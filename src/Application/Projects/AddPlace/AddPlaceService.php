<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

use Application\Projects\AddPlace\AddPlaceCommand;
use Domain\Projects\Entities\Place;
use Domain\Projects\ProjectRepository;
use Domain\Shared\Capacity;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<AddPlaceCommand>
 */
final class AddPlaceService extends ApplicationService implements AddPlace
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddPlaceCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById(id: $command->projectId);
        $place = Place::new(capacity: new Capacity(value: $command->capacity), name: $command->name);
        $project->addPlace(place: $place);
        $this->projectRepository->save($project);
    }
}
