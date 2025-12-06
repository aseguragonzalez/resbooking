<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

use Domain\Projects\Entities\Project;
use Domain\Projects\ProjectRepository;

final class CreateNewProjectService implements CreateNewProject
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(CreateNewProjectCommand $command): void
    {
        $project = Project::new(email: $command->email);

        $this->projectRepository->save($project);
    }
}
