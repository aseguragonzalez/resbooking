<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class RemovePlaceTest extends TestCase
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

    public function testRemovePlaceShouldRemovePlace(): void
    {
        $this->assertTrue(false);
    }

    public function testRemovePlaceShouldFailWhenPlaceDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemovePlaceShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testRemovePlaceShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testRemovePlaceShouldFailWhenUserDoesNotExist(): void
    {
        $this->assertTrue(false);
    }
}
