<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Events\RestaurantModified;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class RestaurantModifiedTest extends TestCase
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
        $restaurant = Restaurant::build(
            id: $restaurantId,
            settings: new Settings(
                email: new Email($this->faker->email),
                hasReminders: $this->faker->boolean,
                name: $this->faker->name,
                maxNumberOfDiners: new Capacity(100),
                minNumberOfDiners: new Capacity(1),
                numberOfTables: new Capacity(25),
                phone: new Phone($this->faker->phoneNumber)
            )
        );

        $event = RestaurantModified::new(restaurantId: $restaurantId, restaurant: $restaurant);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('RestaurantModified', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($restaurant, $payload['restaurant']);
    }
}
