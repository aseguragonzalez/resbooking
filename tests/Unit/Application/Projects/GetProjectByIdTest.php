<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\GetProjectById;

use Application\Projects\GetProjectById\GetProjectByIdCommand;
use Application\Projects\GetProjectById\GetProjectByIdService;
use Domain\Projects\Entities\Project;
use Domain\Projects\Services\ProjectObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class GetProjectByIdTest extends TestCase
{
    private Faker $faker;
    private ProjectBuilder $projectBuilder;
    private MockObject&ProjectObtainer $projectObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectObtainer = $this->createMock(ProjectObtainer::class);
    }

    public function testItRetrievesProjectById(): void
    {
        // Arrange
        $projectId = $this->faker->uuid;
        $project = $this->projectBuilder->build();
        $this->projectObtainer->expects($this->once())
            ->method('obtain')
            ->with($projectId)
            ->willReturn($project);
        $command = new GetProjectByIdCommand(id: $projectId);
        $service = new GetProjectByIdService($this->projectObtainer);

        // Act
        $result = $service->execute($command);

        // Assert
        $this->assertSame($project, $result);
        $this->assertInstanceOf(Project::class, $result);
    }
}
