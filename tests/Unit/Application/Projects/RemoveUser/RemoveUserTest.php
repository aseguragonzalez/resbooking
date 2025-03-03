<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemoveUser;

use App\Application\Projects\RemoveUser\{RemoveUser, RemoveUserRequest};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\User;
use App\Domain\Shared\{Email};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class RemoveUserTest extends TestCase
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

    public function testRemoveUserShouldUpdateProjectWithoutUser(): void
    {
        $email = $this->faker->email;
        $users = [
            new User(username: new Email($this->faker->email)),
            new User(username: new Email($email)),
        ];
        $project = $this->projectBuilder->withUsers($users)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new RemoveUserRequest(projectId: $this->faker->uuid, username: $email);
        $useCase = new RemoveUser(projectRepository: $this->projectRepository);

        $useCase->execute($request);

        $this->assertSame(1, count($project->getUsers()));
    }
}
