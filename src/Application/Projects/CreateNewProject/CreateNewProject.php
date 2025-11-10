<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

interface CreateNewProject
{
    public function execute(CreateNewProjectCommand $command): void;
}
