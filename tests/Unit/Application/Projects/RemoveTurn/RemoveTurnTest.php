<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemoveTurn;

use App\Application\Projects\RemoveTurn\{RemoveTurn, RemoveTurnCommand};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class RemoveTurnTest extends TestCase
{
    private Faker $faker;
    private ProjectBuilder $projectBuilder;
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

    public function testRemoveTurnFromProject(): void
    {
        $turn = new TurnAvailability(new Capacity(10), DayOfWeek::Monday, Turn::H1200);
        $turns = [
            $turn,
            new TurnAvailability(new Capacity(10), DayOfWeek::Monday, Turn::H1230),
        ];
        $project = $this->projectBuilder->withTurns($turns)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new RemoveTurnCommand(
            projectId: $this->faker->uuid,
            turn: Turn::H1200,
            dayOfWeek: DayOfWeek::Monday
        );
        $ApplicationService = new RemoveTurn(projectRepository: $this->projectRepository);

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getTurns()));
    }
}
