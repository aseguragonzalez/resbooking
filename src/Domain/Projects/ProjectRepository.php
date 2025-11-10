<?php

declare(strict_types=1);

namespace Domain\Projects;

use Domain\Projects\Entities\Project;
use Seedwork\Domain\Repository;

/**
 * @extends Repository<Project>
 */
interface ProjectRepository extends Repository
{
}
