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
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnsShouldFailWhenTurnsDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnsShouldFailWhenUserDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnsShouldDoNothingWhenTurnsIsEmpty(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
