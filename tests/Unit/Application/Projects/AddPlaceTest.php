<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddPlace;

use Application\Projects\AddPlace\AddPlaceCommand;
use Application\Projects\AddPlace\AddPlaceService;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
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
    private MockObject&ProjectObtainer $projectObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectObtainer = $this->createMock(ProjectObtainer::class);
    }

    public function testCreateNewPlace(): void
    {
        $project = $this->projectBuilder->build();
        $this->projectObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->isString())
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $ApplicationService = new AddPlaceService($this->projectObtainer, $this->projectRepository);
        $request = new AddPlaceCommand(
            projectId: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->randomNumber(2)
        );

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getPlaces()));
    }
}
