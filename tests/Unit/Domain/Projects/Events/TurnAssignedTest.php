<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use App\Domain\Projects\Events\TurnAssigned;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Domain\Shared\{DayOfWeek, Capacity, Turn};
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

    public function testNewShouldCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::new(projectId: $projectId, turn: $turn);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('TurnAssigned', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($turn, $payload['turn']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $turn = new TurnAvailability(
            capacity: new Capacity(value: $this->faker->numberBetween(1, 100)),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $event = TurnAssigned::build(projectId: $projectId, turn: $turn, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('TurnAssigned', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($turn, $payload['turn']);
    }
}
