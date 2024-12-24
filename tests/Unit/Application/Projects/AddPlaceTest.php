<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class AddPlaceTest extends TestCase
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

    public function testAddPlaceShouldCreateNewPlace(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenPlaceAlreadyExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenUserDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenNameIsDuplicate(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
