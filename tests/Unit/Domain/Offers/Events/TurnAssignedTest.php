<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use Domain\Offers\Events\TurnAssigned;
use Domain\Shared\ValueObjects\TurnAvailability;
use Domain\Shared\{DayOfWeek, Capacity, Turn};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class TurnAssignedTest extends TestCase
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
        $offerId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::new(offerId: $offerId, turn: $turn);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnAssigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($offerId, $payload['offerId']);
        $this->assertSame($turn, $payload['turn']);
    }

    public function testBuildStoredEvent(): void
    {
        $offerId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::build(offerId: $offerId, turn: $turn, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnAssigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($offerId, $payload['offerId']);
        $this->assertSame($turn, $payload['turn']);
    }
}
