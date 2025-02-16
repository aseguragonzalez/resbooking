<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddTurns;

use App\Application\Projects\AddTurns\{AddTurns, AddTurnsRequest, TurnItem};
use App\Domain\Projects\ProjectRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class AddTurnsTest extends TestCase
{
    private Faker $faker;
    private ProjectBuilder $projectBuilder ;
    private MockObject&ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
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

        /** @var int $day1 */
        $day1 = $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]);
        /** @var int $day2 */
        $day2 = $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]);
        $request = new AddTurnsRequest(
            projectId: $this->faker->uuid,
            turns: [
                new TurnItem(
                    capacity: $this->faker->numberBetween(1, 100),
                    dayOfWeek: $day1,
                    startTime: '13:00'
                ),
                new TurnItem(
                    capacity: $this->faker->numberBetween(1, 100),
                    dayOfWeek: $day2,
                    startTime: '14:00'
                ),
            ]
        );
        $useCase = new AddTurns($this->projectRepository);

        $useCase->execute($request);

        $this->assertEquals(count($request->turns), count($project->getTurns()));
    }
}
