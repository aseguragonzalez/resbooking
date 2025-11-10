<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

use Domain\Projects\ProjectRepository;
use Domain\Projects\Entities\Project;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<CreateNewProjectCommand>
 */
final class CreateNewProject extends ApplicationService
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param CreateNewProjectCommand $command
     */
    public function execute($command): void
    {
        $project = Project::new(email: $command->email);

        $this->projectRepository->save($project);
    }
}
