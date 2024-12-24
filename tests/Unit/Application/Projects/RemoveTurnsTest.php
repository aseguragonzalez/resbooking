<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class RemoveTurnsTest extends TestCase
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

    public function testRemoveTurnsShouldRemoveTurns(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveTurnsShouldFailWhenTurnsDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveTurnsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveTurnsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveTurnsShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemoveTurnsShouldDoNothingWhenTurnsIsEmpty(): void
    {
        $this->assertTrue(false);
    }
}
