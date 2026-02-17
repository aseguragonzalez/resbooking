<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\AvailabilitiesUpdated;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\EntityId;
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
        $restaurantId = $this->faker->uuid();
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

        $event = AvailabilitiesUpdated::new(
            restaurantId: EntityId::fromString($restaurantId),
            availabilities: $availabilities
        );

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('AvailabilitiesUpdated', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($availabilities, $payload['availabilities']);
    }
}
