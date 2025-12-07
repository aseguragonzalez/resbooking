<?php

declare(strict_types=1);

namespace Domain\Projects\Events;

use Domain\Projects\Entities\Project;
use Seedwork\Domain\DomainEvent;

final readonly class ProjectDeleted extends DomainEvent
{
    public static function new(string $projectId, Project $project): self
    {
        return new self(
            id: uniqid(),
            type: 'ProjectDeleted',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }
}
