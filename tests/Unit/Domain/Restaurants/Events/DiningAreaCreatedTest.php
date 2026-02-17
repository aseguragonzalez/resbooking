<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\DiningAreaCreated;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;

final class DiningAreaCreatedTest extends TestCase
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
        $restaurantId = $this->faker->uuid;
        $diningArea = DiningArea::build(
            id: $this->faker->uuid,
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            name: $this->faker->name
        );

        $event = DiningAreaCreated::new(restaurantId: EntityId::fromString($restaurantId), diningArea: $diningArea);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('DiningAreaCreated', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($diningArea, $payload['diningArea']);
    }
}
