<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Domain\Projects\Events\TurnAssigned;
use Domain\Projects\ValueObjects\TurnAvailability;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Capacity;
use Domain\Shared\Turn;
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
        $projectId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::new(projectId: $projectId, turn: $turn);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnAssigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($turn, $payload['turn']);
    }
}
