<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemoveOpenCloseEvent;

use App\Application\Projects\RemoveOpenCloseEvent\{RemoveOpenCloseEvent, RemoveOpenCloseEventRequest};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
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

    public function testRemoveOpenCloseEventShouldUpdateProjectWithoutEvent(): void
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
        $request = new RemoveOpenCloseEventRequest(projectId: $this->faker->uuid, turn: Turn::H1200, date: $today);
        $useCase = new RemoveOpenCloseEvent(projectRepository: $this->projectRepository);

        $useCase->execute($request);

        $this->assertSame(1, count($project->getOpenCloseEvents()));
    }
}
