<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddPlace;

use Application\Projects\AddPlace\AddPlace;
use Application\Projects\AddPlace\AddPlaceCommand;
use Domain\Projects\ProjectRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class AddPlaceTest extends TestCase
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

    public function testCreateNewPlace(): void
    {
        $project = $this->projectBuilder->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->with($this->isString())
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $ApplicationService = new AddPlace($this->projectRepository);
        $request = new AddPlaceCommand(
            projectId: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->randomNumber(2)
        );

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getPlaces()));
    }
}
