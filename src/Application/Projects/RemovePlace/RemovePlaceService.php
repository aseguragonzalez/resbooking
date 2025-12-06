<?php

declare(strict_types=1);

namespace Application\Projects\RemovePlace;

use Domain\Projects\Entities\Place;
use Domain\Projects\ProjectRepository;
use Seedwork\Application\ApplicationService;

/**
 * @template-extends ApplicationService<RemovePlaceCommand>
 * @extends ApplicationService<RemovePlaceCommand>
 */
final class RemovePlaceService extends ApplicationService implements RemovePlace
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemovePlaceCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $project->removePlaces(fn (Place $place) => $place->getId() === $command->placeId);
        $this->projectRepository->save($project);
    }
}
