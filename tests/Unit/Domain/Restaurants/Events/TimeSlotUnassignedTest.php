<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\TimeSlotUnassigned;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class TimeSlotUnassignedTest extends TestCase
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
        $availability = new Availability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200,
        );

        $event = TimeSlotUnassigned::create($restaurantId, $availability);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('time_slot.unassigned', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId->value, $payload['restaurant_id']);
        $this->assertSame(DayOfWeek::Monday->value, $payload['day_of_week_id']);
        $this->assertSame(TimeSlot::H1200->value, $payload['time_slot_id']);
    }
}
