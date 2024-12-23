<?php

declare(strict_types=1);

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
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenPlaceAlreadyExists(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenNameIsDuplicate(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testAddPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->assertTrue(false);
    }
}
