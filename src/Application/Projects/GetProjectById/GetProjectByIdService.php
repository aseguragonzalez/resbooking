<?php

declare(strict_types=1);

namespace Application\Projects\GetProjectById;

use Domain\Projects\Entities\Project;
use Domain\Projects\Services\ProjectObtainer;

final readonly class GetProjectByIdService implements GetProjectById
{
    public function __construct(private ProjectObtainer $projectObtainer)
    {
    }

    public function execute(GetProjectByIdCommand $command): Project
    {
        return $this->projectObtainer->obtain($command->id);
    }
}
