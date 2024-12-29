<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class AddUserTest extends TestCase
{
    private $faker = null;
    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectRepository = null;
    }

    public function testAddUserShouldCreateNewAdmin(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldCreateNewUser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldSendEmailToNewUser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenNewUserAlreadyExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenCreatorIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenCreatorDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenUsernameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddUserShouldFailWhenUsernameIsDuplicate(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
