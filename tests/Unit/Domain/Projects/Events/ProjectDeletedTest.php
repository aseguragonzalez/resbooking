<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Events\ProjectDeleted;
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\{Capacity, Email, Phone};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class ProjectDeletedTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $project = Project::build(
            id: $projectId,
            settings: new Settings(
                email: new Email($this->faker->email),
                hasRemainders: $this->faker->boolean,
                name: $this->faker->name,
                maxNumberOfDiners: new Capacity(100),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(25),
                phone: new Phone($this->faker->phoneNumber)
            )
        );

        $event = ProjectDeleted::new(projectId: $projectId, project: $project);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('ProjectDeleted', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($project, $payload['project']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $project = Project::build(
            id: $projectId,
            settings: new Settings(
                email: new Email($this->faker->email),
                hasRemainders: $this->faker->boolean,
                name: $this->faker->name,
                maxNumberOfDiners: new Capacity(100),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(25),
                phone: new Phone($this->faker->phoneNumber)
            )
        );

        $event = ProjectDeleted::build(projectId: $projectId, project: $project, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('ProjectDeleted', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($project, $payload['project']);
    }
}
