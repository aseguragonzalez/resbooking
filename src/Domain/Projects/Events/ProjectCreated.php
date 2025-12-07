<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\Entities\Project;
use Seedwork\Domain\DomainEvent;

final readonly class ProjectCreated extends DomainEvent
{
    public static function new(string $projectId, Project $project): self
    {
        return new self(
            id: uniqid(),
            type: 'ProjectCreated',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }
}
