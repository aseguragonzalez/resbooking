<?php

declare(strict_types=1);

namespace App\Application\Projects\AddPlace;

use App\Application\Projects\AddPlace\AddPlaceCommand;
use App\Domain\Projects\Entities\{Project, Place};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\Capacity;
use Seedwork\Application\{ApplicationService, Command};

/**
 * @extends ApplicationService<AddPlaceCommand>
 */
final class AddPlace extends ApplicationService
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
