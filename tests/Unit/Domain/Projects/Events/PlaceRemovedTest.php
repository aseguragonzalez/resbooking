<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Domain\Projects\Entities\Place;
use Domain\Projects\Events\PlaceRemoved;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PlaceRemovedTest extends TestCase
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

        $event = PlaceRemoved::new(projectId: $projectId, place: $place);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('PlaceRemoved', $event->getType());
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

        $event = PlaceRemoved::build(projectId: $projectId, place: $place, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('PlaceRemoved', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($place, $payload['place']);
    }
}
