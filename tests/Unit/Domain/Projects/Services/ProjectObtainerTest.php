<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Services;

use Domain\Projects\Entities\Project;
use Domain\Projects\Exceptions\ProjectDoesNotExist;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ProjectObtainerTest extends TestCase
{
    private Faker $faker;
    private MockObject&ProjectRepository $projectRepository;
    private ProjectObtainer $projectObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectObtainer = new ProjectObtainer($this->projectRepository);
    }

    public function testObtainReturnsProjectWhenItExists(): void
    {
        $projectId = $this->faker->uuid;
        $project = Project::new(email: $this->faker->email, id: $projectId);
        $this->projectRepository->expects($this->once())
            ->method('getById')
            ->with($projectId)
            ->willReturn($project);


        $result = $this->projectObtainer->obtain($projectId);

        $this->assertInstanceOf(Project::class, $result);
        $this->assertSame($project, $result);
        $this->assertSame($projectId, $result->getId());
    }

    public function testObtainThrowsExceptionWhenProjectDoesNotExist(): void
    {
        $projectId = $this->faker->uuid;
        $this->projectRepository->expects($this->once())
            ->method('getById')
            ->with($projectId)
            ->willReturn(null);
        $this->expectException(ProjectDoesNotExist::class);

        $this->projectObtainer->obtain($projectId);
    }
}
