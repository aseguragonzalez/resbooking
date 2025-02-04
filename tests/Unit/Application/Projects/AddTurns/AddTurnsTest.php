<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddTurns;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Application\Projects\AddTurns\{AddTurns, AddTurnsRequest, TurnItem};
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ProjectRepository;
use Tests\Unit\ProjectBuilder;

final class AddTurnsTest extends TestCase
{
    private $faker = null;
    private ?ProjectBuilder $projectBuilder = null;
    private ?ProjectRepository $projectRepository = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectBuilder = null;
        $this->projectRepository = null;
    }

    public function testAddTurnsShouldCreateNewTurns(): void
    {
        $project = $this->projectBuilder->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->with($this->isType('string'))
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new AddTurnsRequest(
            projectId: $this->faker->uuid,
            turns: [
                new TurnItem(
                    capacity: $this->faker->numberBetween(1, 100),
                    dayOfWeek: $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
                    startTime: '13:00'
                ),
                new TurnItem(
                    capacity: $this->faker->numberBetween(1, 100),
                    dayOfWeek: $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
                    startTime: '14:00'
                ),
            ]
        );
        $useCase = new AddTurns($this->projectRepository);

        $useCase->execute($request);

        $this->assertEquals(count($request->turns), count($project->getTurns()));
    }
}
