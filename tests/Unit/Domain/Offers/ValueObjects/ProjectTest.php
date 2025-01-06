<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\ValueObjects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\ValueObjects\Project;
use App\Seedwork\Domain\Exceptions\ValueException;

final class ProjectTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testProjectShouldCreateInstance(): void
    {
        $projectId = $this->faker->uuid;

        $project = new Project(id: $projectId);

        $this->assertEquals($project->id, $projectId);
    }

    public function testProjectShouldFailsWhenIdIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Project(id: '');
    }
}
