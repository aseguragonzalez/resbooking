<?php

declare(strict_types=1);

namespace App\Application\Projects\RemovePlace;

use App\Domain\Projects\Entities\Place;
use App\Domain\Projects\ProjectRepository;
use Seedwork\Application\ApplicationService;

/**
 * @template-extends ApplicationService<RemovePlaceCommand>
 * @extends ApplicationService<RemovePlaceCommand>
 */
final class RemovePlace extends ApplicationService
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
