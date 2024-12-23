<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class ModifyPlaceTest extends TestCase
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

    public function testModifyPlaceShouldUpdatePlace(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenPlaceDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testModifyPlaceShouldFailWhenNameIsDuplicate(): void
    {
        $this->assertTrue(false);
    }
}
