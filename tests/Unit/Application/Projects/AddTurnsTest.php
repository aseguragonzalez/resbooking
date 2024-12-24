<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class AddTurnsTest extends TestCase
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

    public function testAddTurnsShouldCreateNewTurns(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenTurnsAlreadyExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenUserDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenDayOfWeekDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenTurnDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldDoNothingWhenTurnsIsEmpty(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
