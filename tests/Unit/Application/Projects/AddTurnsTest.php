<?php

declare(strict_types=1);

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
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenTurnsAlreadyExists(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenCapacityIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenDayOfWeekDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldFailWhenTurnDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddTurnsShouldDoNothingWhenTurnsIsEmpty(): void
    {
        $this->assertTrue(false);
    }
}
