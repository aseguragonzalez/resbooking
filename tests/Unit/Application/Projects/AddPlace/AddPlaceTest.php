<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddPlace;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Application\Projects\AddPlace\{AddPlace, AddPlaceRequest};
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ProjectRepository;
use Tests\Unit\ProjectBuilder;

final class AddPlaceTest extends TestCase
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

    public function testAddPlaceShouldCreateNewPlace(): void
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
        $useCase = new AddPlace($this->projectRepository);
        $request = new AddPlaceRequest(
            projectId: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->randomNumber(2)
        );

        $useCase->execute($request);

        $this->assertEquals(1, count($project->getPlaces()));
    }
}
