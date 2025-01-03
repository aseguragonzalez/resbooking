<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Events\OpenCloseEventCreated;
use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;

final class OpenCloseEventCreatedTest extends TestCase
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
        $projectId = $this->faker->uuid;
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $event = OpenCloseEventCreated::new(
            projectId: $projectId,
            openCloseEvent: $openCloseEvent
        );

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('OpenCloseEventCreated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($openCloseEvent, $payload['openCloseEvent']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $event = OpenCloseEventCreated::build(
            projectId: $projectId,
            openCloseEvent: $openCloseEvent,
            id: $this->faker->uuid
        );

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('OpenCloseEventCreated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($openCloseEvent, $payload['openCloseEvent']);
    }
}
