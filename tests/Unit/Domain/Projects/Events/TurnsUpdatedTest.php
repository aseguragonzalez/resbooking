<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Domain\Projects\Events\TurnsUpdated;
use Domain\Projects\ValueObjects\TurnAvailability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class TurnsUpdatedTest extends TestCase
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
        $projectId = $this->faker->uuid();
        $turns = [
            new TurnAvailability(
                capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
                dayOfWeek: DayOfWeek::Monday,
                turn: Turn::H1200,
            ),
            new TurnAvailability(
                capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
                dayOfWeek: DayOfWeek::Tuesday,
                turn: Turn::H1230,
            ),
        ];

        $event = TurnsUpdated::new(projectId: $projectId, turns: $turns);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnsUpdated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($turns, $payload['turns']);
    }
}
