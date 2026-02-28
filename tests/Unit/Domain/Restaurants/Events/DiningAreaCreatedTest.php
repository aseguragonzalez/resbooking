<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\DiningAreaCreated;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

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
        $restaurantId = RestaurantId::fromString($this->faker->uuid);
        $diningArea = DiningArea::build(
            id: $this->faker->uuid,
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            name: $this->faker->name
        );

        $event = DiningAreaCreated::create($restaurantId, $diningArea);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('dining_area.created', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId->value, $payload['restaurant_id']);
        $this->assertSame($diningArea->id->value, $payload['dining_area_id']);
        $this->assertSame($diningArea->name, $payload['name']);
        $this->assertSame($diningArea->capacity->value, $payload['capacity']);
    }
}
