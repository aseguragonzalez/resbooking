<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdatePlace;

use Application\Projects\UpdatePlace\UpdatePlaceCommand;
use Application\Projects\UpdatePlace\UpdatePlaceService;
use Domain\Projects\Entities\Place;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class UpdatePlaceTest extends TestCase
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

    public function testUpdatePlaceInProject(): void
    {
        $placeId = $this->faker->uuid();
        $originalPlace = Place::build(
            id: $placeId,
            capacity: new Capacity(10),
            name: $this->faker->name()
        );
        $places = [$originalPlace];
        $project = $this->projectBuilder->withPlaces($places)->build();
        $this->projectObtainer->expects($this->once())
            ->method('obtain')
            ->with($project->getId())
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);

        $newName = $this->faker->name();
        $newCapacity = 20;
        $request = new UpdatePlaceCommand(
            projectId: $project->getId(),
            placeId: $placeId,
            name: $newName,
            capacity: $newCapacity
        );
        $applicationService = new UpdatePlaceService($this->projectObtainer, $this->projectRepository);

        $applicationService->execute($request);

        $this->assertSame(1, count($project->getPlaces()));
        $updatedPlace = $project->getPlaces()[0];
        $this->assertSame($placeId, $updatedPlace->getId());
        $this->assertSame($newName, $updatedPlace->name);
        $this->assertSame($newCapacity, $updatedPlace->capacity->value);
    }
}
