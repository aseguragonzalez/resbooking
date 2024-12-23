<?php

declare(strict_types=1);

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
        $this->assertTrue(false);
    }

    public function testAddUserShouldCreateNewUser(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldSendEmailToNewUser(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenNewUserAlreadyExists(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenCreatorIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenCreatorDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenUsernameIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testAddUserShouldFailWhenUsernameIsDuplicate(): void
    {
        $this->assertTrue(false);
    }
}
