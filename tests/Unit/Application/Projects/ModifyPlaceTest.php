<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

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
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenPlaceDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenUserDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenNameIsDuplicate(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
