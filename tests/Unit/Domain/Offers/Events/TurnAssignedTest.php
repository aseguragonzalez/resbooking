<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\Events\TurnAssigned;
use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
use App\Domain\Shared\ValueObjects\TurnAvailability;

final class TurnAssignedTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $offerId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::new(offerId: $offerId, turn: $turn);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('TurnAssigned', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($offerId, $payload['offerId']);
        $this->assertEquals($turn, $payload['turn']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $offerId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::build(offerId: $offerId, turn: $turn, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('TurnAssigned', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($offerId, $payload['offerId']);
        $this->assertEquals($turn, $payload['turn']);
    }
}
