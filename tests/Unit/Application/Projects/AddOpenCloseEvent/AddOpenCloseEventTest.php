<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddOpenCloseEvent;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Application\Projects\AddOpenCloseEvent\{AddOpenCloseEvent, AddOpenCloseEventRequest};
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ProjectRepository;
use Tests\Unit\ProjectBuilder;

final class AddOpenCloseEventTest extends TestCase
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

    public function testAddOpenCloseEventShouldCreateNewOpenCloseEvent(): void
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
        $nowPlusOneHour = (new \DateTimeImmutable())->modify('+1 hour');
        $request = new AddOpenCloseEventRequest(
            projectId: $this->faker->uuid,
            date: $nowPlusOneHour,
            isAvailable: $this->faker->boolean,
            startTime: '13:00'
        );
        $useCase = new AddOpenCloseEvent($this->projectRepository);

        $useCase->execute($request);

        $this->assertEquals(1, count($project->getOpenCloseEvents()));
    }
}
