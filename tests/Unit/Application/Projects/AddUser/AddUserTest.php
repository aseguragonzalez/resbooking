<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddUser;

use App\Application\Projects\AddUser\{AddUser, AddUserRequest};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Users\{UserFactory, UserRepository};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class AddUserTest extends TestCase
{
    private Faker $faker;
    private ProjectBuilder $projectBuilder;
    private MockObject&ProjectRepository $projectRepository;
    private MockObject&UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewAdmin(): void
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
        $this->userRepository
            ->expects($this->once())
            ->method('save');
        $request = new AddUserRequest(
            projectId: $this->faker->uuid,
            username: $this->faker->email,
            isAdmin: true
        );
        $useCase = new AddUser(
            projectRepository: $this->projectRepository,
            userFactory: new UserFactory(),
            userRepository: $this->userRepository
        );

        $useCase->execute($request);

        $this->assertSame(1, count($project->getUsers()));
    }

    public function testCreateNewUser(): void
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
        $this->userRepository
            ->expects($this->once())
            ->method('save');
        $request = new AddUserRequest(
            projectId: $this->faker->uuid,
            username: $this->faker->email,
            isAdmin: false
        );
        $useCase = new AddUser(
            projectRepository: $this->projectRepository,
            userFactory: new UserFactory(),
            userRepository: $this->userRepository
        );

        $useCase->execute($request);

        $this->assertSame(1, count($project->getUsers()));
    }
}
