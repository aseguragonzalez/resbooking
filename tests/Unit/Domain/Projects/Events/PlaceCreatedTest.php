<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use App\Domain\Projects\Entities\Place;
use App\Domain\Projects\Events\PlaceCreated;
use App\Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PlaceCreatedTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $place = Place::build(
            id: $this->faker->uuid,
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            name: $this->faker->name
        );

        $event = PlaceCreated::new(projectId: $projectId, place: $place);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('PlaceCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($place, $payload['place']);
    }

    public function testBuildStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $place = Place::build(
            id: $this->faker->uuid,
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            name: $this->faker->name
        );

        $event = PlaceCreated::build(projectId: $projectId, place: $place, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('PlaceCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($place, $payload['place']);
    }
}
