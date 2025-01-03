<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\Project;
use App\Seedwork\Domain\DomainEvent;

final class ProjectCreated extends DomainEvent
{
    public static function new(string $projectId, Project $project): self
    {
        return new self(
            type: 'ProjectCreated',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }

    public static function build(string $projectId, Project $project, string $id): self
    {
        return new self(
            id: $id,
            type: 'ProjectCreated',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }
}
