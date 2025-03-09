<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Projects\Entities\Project;
use Seedwork\Domain\Repository;

/**
 * @extends Repository<Project>
 */
interface ProjectRepository extends Repository
{
}
