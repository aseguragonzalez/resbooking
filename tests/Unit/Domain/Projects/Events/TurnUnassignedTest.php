<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use App\Domain\Projects\Events\TurnUnassigned;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class TurnUnassignedTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $event = TurnUnassigned::new(projectId: $projectId, turn: $turn);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnUnassigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($turn, $payload['turn']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $event = TurnUnassigned::build(projectId: $projectId, turn: $turn, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('TurnUnassigned', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($turn, $payload['turn']);
    }
}
