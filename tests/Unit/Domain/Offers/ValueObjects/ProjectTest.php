<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\ValueObjects;

use App\Domain\Offers\ValueObjects\Project;
use Seedwork\Domain\Exceptions\ValueException;
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

    public function testCreateInstance(): void
    {
        $projectId = $this->faker->uuid;

        $project = new Project(id: $projectId);

        $this->assertSame($project->id, $projectId);
    }

    public function testCreateInstanceFailsWhenIdIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Project(id: '');
    }
}
