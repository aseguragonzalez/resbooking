<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Projects\Events\ProjectCreated;
use App\Domain\Shared\{Capacity, Email, Phone};

final class ProjectCreatedTest extends TestCase
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

    public function testNewShouldCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $project = Project::stored(
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

        $event = ProjectCreated::new(projectId: $projectId, project: $project);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('ProjectCreated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($project, $payload['project']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $project = Project::stored(
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

        $event = ProjectCreated::build(projectId: $projectId, project: $project, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('ProjectCreated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($project, $payload['project']);
    }
}
