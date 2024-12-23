<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class RemoveUserTest extends TestCase
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

    public function testRemoveUserShouldDeleteUser(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveUserShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveUserShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveUserShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }
}
