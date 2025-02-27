<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Projects;

final readonly class ProjectUser
{
    public function __construct(public int $projectId, public int $userId)
    {
    }
}
