<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemoveOpenCloseEvent;

use Application\Projects\RemoveOpenCloseEvent\{RemoveOpenCloseEvent, RemoveOpenCloseEventCommand};
use Domain\Projects\ProjectRepository;
use Domain\Shared\Turn;
use Domain\Shared\ValueObjects\OpenCloseEvent;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class RemoveOpenCloseEventTest extends TestCase
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

    public function testRemoveOpenCloseEventFromProject(): void
    {
        $today = new \DateTimeImmutable("now");
        $event = new OpenCloseEvent(date: $today, isAvailable: true, turn: Turn::H1200);
        $events = [
            $event,
            new OpenCloseEvent(date: new \DateTimeImmutable(), isAvailable: true, turn: Turn::H1230),
        ];
        $project = $this->projectBuilder->withOpenCloseEvents($events)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new RemoveOpenCloseEventCommand(projectId: $this->faker->uuid, turn: Turn::H1200, date: $today);
        $ApplicationService = new RemoveOpenCloseEvent(projectRepository: $this->projectRepository);

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getOpenCloseEvents()));
    }
}
