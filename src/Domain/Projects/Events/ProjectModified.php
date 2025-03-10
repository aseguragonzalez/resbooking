<?php

declare(strict_types=1);

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Entities\Project;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class ProjectModified extends DomainEvent
{
    public static function new(string $projectId, Project $project): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'ProjectModified',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }

    public static function build(string $projectId, Project $project, string $id): self
    {
        return new self(
            id: $id,
            type: 'ProjectModified',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }
}
