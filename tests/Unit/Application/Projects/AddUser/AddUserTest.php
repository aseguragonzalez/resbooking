<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddUser;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Application\Projects\AddUser\{AddUser, AddUserRequest};
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\Role;
use App\Domain\Users\{UserFactory, UserRepository};
use Tests\Unit\ProjectBuilder;

final class AddUserTest extends TestCase
{
    private $faker = null;
    private ?ProjectBuilder $projectBuilder = null;
    private ?ProjectRepository $projectRepository = null;
    private ?UserRepository $userRepository = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectBuilder = null;
        $this->projectRepository = null;
        $this->userRepository = null;
    }

    public function testAddUserShouldCreateNewUser(): void
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
        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $request = new AddUserRequest(
            projectId: $this->faker->uuid,
            username: $this->faker->email,
            isAdmin: $this->faker->boolean
        );
        $useCase = new AddUser(
            projectRepository: $this->projectRepository,
            userFactory: new UserFactory(),
            userRepository: $this->userRepository
        );

        $useCase->execute($request);

        $this->assertEquals(1, count($project->getUsers()));
    }
}
