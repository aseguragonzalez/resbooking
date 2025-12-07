<?php

declare(strict_types=1);

namespace Application\Projects\GetProjectById;

use Domain\Projects\Entities\Project;

interface GetProjectById
{
    public function execute(GetProjectByIdCommand $id): Project;
}
