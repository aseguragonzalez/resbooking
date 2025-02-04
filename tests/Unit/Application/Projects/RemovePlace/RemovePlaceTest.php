<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemovePlace;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Application\Projects\RemovePlace\{RemovePlace, RemovePlaceRequest};
use App\Domain\Projects\Entities\{Place, Project};
use App\SeedWork\Domain\{Capacity};
use App\Domain\Projects\ProjectRepository;
use Tests\Unit\ProjectBuilder;

final class RemovePlaceTest extends TestCase
{
    private $faker = null;
    private $projectBuilder = null;
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

    public function testRemovePlaceShouldRemovePlace(): void
    {
        $places = [new Place(
            id: $this->faker->uuid,
            capacity: new Capacity()
        )];
        $project = $this->projectBuilder->withPlaces($places)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->with($this->isType('string'))
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $useCase = new RemovePlace($this->projectRepository);
        $request = new RemovePlaceRequest(
            projectId: $this->faker->uuid,
            placeId: $this->faker->uuid
        );

        $useCase->execute($request);

        $this->assertEquals(0, count($project->getPlaces()));
    }
}
