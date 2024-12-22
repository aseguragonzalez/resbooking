<?php

declare(strict_types=1);

namespace App\Application\Projects\CreateProject;

use App\Application\Projects\CreateProject\CreateProjectRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class CreateProject extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(CreateProjectRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
