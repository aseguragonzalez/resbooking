<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Application\Projects\CreateNewProject\CreateNewProject;
use Application\Projects\CreateNewProject\CreateNewProjectCommand;
use Domain\Projects\ProjectRepository;
use Domain\Projects\Entities\Project;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateNewProjectTest extends TestCase
{
    private MockObject&ProjectRepository $projectRepository;
    private CreateNewProject $service;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->service = new CreateNewProject($this->projectRepository);
    }

    public function testExecuteCreatesAndSavesProject(): void
    {
        $command = new CreateNewProjectCommand('test@example.com');
        $this->projectRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($project) {
                return $project instanceof Project;
            }));

        $this->service->execute($command);
    }
}
