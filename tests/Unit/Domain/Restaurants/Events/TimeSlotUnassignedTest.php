<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\TimeSlotUnassigned;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Capacity;
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
        $restaurantId = $this->faker->uuid;
        $availability = new Availability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200,
        );

        $event = TimeSlotUnassigned::new(restaurantId: $restaurantId, availability: $availability);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TimeSlotUnassigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($availability, $payload['availability']);
    }
}
