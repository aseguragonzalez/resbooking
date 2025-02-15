<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\ValueObjects;

use App\Domain\Offers\ValueObjects\Project;
use App\Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
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
