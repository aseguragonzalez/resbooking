<?php

declare(strict_types=1);

namespace Domain\Projects\Services;

use Domain\Projects\Entities\Project;
use Domain\Projects\Exceptions\ProjectDoesNotExist;
use Domain\Projects\Repositories\ProjectRepository;

readonly class ProjectObtainer
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function obtain(string $id): Project
    {
        $project = $this->projectRepository->getById($id);
        if ($project === null) {
            throw new ProjectDoesNotExist();
        }
        return $project;
    }
}
