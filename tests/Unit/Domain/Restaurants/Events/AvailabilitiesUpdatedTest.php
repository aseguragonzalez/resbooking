<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\AvailabilitiesUpdated;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class AvailabilitiesUpdatedTest extends TestCase
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
        $restaurantId = RestaurantId::fromString($this->faker->uuid());
        $availabilities = [
            new Availability(
                capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
                dayOfWeek: DayOfWeek::Monday,
                timeSlot: TimeSlot::H1200,
            ),
            new Availability(
                capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
                dayOfWeek: DayOfWeek::Tuesday,
                timeSlot: TimeSlot::H1230,
            ),
        ];

        $event = AvailabilitiesUpdated::create($restaurantId, $availabilities);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('availabilities.updated', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId->value, $payload['restaurant_id']);
        $this->assertIsArray($payload['availabilities']);
        $this->assertCount(2, $payload['availabilities']);
        /** @var array<string, mixed> $availability */
        $availability = $payload['availabilities'][0];
        $this->assertSame(DayOfWeek::Monday->value, $availability['day_of_week_id']);
        $this->assertSame(TimeSlot::H1200->value, $availability['time_slot_id']);
    }
}
