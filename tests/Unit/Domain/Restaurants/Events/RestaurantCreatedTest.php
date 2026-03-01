<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\RestaurantCreated;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class RestaurantCreatedTest extends TestCase
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

        $event = RestaurantCreated::create($restaurantId);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('restaurant.created', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId->value, $payload['restaurant_id']);
    }
}
