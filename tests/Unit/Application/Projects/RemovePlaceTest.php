<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemovePlace;

use Application\Projects\RemovePlace\RemovePlace;
use Application\Projects\RemovePlace\RemovePlaceCommand;
use Application\Projects\RemovePlace\RemovePlaceService;
use Domain\Projects\Entities\Place;
use Domain\Projects\ProjectRepository;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class RemovePlaceTest extends TestCase
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

    public function testPlaceFromProject(): void
    {
        $place = Place::new(new Capacity(10), name: $this->faker->name);
        $places = [
            $place,
            Place::new(new Capacity(10), name: $this->faker->name),
        ];
        $project = $this->projectBuilder->withPlaces($places)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new RemovePlaceCommand(projectId: $this->faker->uuid, placeId: $place->getId());
        $ApplicationService = new RemovePlaceService(projectRepository: $this->projectRepository);

        $ApplicationService->execute($request);

        $this->assertSame(1, count($project->getPlaces()));
    }
}
