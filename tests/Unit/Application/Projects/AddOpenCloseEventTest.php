<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddOpenCloseEvent;

use Application\Projects\AddOpenCloseEvent\AddOpenCloseEvent;
use Application\Projects\AddOpenCloseEvent\AddOpenCloseEventCommand;
use Application\Projects\AddOpenCloseEvent\AddOpenCloseEventService;
use Domain\Projects\ProjectRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class AddOpenCloseEventTest extends TestCase
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

    public function testCreateNewOpenCloseEvent(): void
    {
        $project = $this->projectBuilder->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $nowPlusOneHour = (new \DateTimeImmutable())->modify('+1 hour');
        $request = new AddOpenCloseEventCommand(
            projectId: $this->faker->uuid,
            date: $nowPlusOneHour,
            isAvailable: $this->faker->boolean,
            startTime: '13:00'
        );
        $ApplicationService = new AddOpenCloseEventService($this->projectRepository);

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getOpenCloseEvents()));
    }
}
