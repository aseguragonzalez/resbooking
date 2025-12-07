<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdateTurns;

use Application\Projects\UpdateTurns\TurnAvailability;
use Application\Projects\UpdateTurns\UpdateTurnsCommand;
use Application\Projects\UpdateTurns\UpdateTurnsService;
use Domain\Projects\Entities\Project;
use Domain\Projects\ProjectRepository;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
use PHPUnit\Framework\TestCase;

final class UpdateTurnsServiceTest extends TestCase
{
    public function testExecuteUpdatesTurns(): void
    {
        $projectId = 'test-project-id';
        $project = Project::new('test@example.com', $projectId);
        $repository = $this->createMock(ProjectRepository::class);

        $repository->expects($this->once())
            ->method('getById')
            ->with($projectId)
            ->willReturn($project);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Project $savedProject) use ($project) {
                return $savedProject === $project;
            }));

        $command = new UpdateTurnsCommand(
            projectId: $projectId,
            turns: [
                new TurnAvailability(
                    dayOfWeekId: DayOfWeek::Monday->value,
                    turnId: Turn::H1200->value,
                    capacity: 15,
                ),
                new TurnAvailability(
                    dayOfWeekId: DayOfWeek::Tuesday->value,
                    turnId: Turn::H1230->value,
                    capacity: 20,
                ),
            ],
        );

        $service = new UpdateTurnsService($repository);
        $service->execute($command);

        $turns = $project->getTurns();
        $this->assertCount(2, $turns);
        $this->assertSame(15, $turns[0]->capacity->value);
        $this->assertSame(DayOfWeek::Monday, $turns[0]->dayOfWeek);
        $this->assertSame(Turn::H1200, $turns[0]->turn);
        $this->assertSame(20, $turns[1]->capacity->value);
        $this->assertSame(DayOfWeek::Tuesday, $turns[1]->dayOfWeek);
        $this->assertSame(Turn::H1230, $turns[1]->turn);
    }
}
