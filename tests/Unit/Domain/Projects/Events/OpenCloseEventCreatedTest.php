<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use App\Domain\Projects\Events\OpenCloseEventCreated;
use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OpenCloseEventCreatedTest extends TestCase
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
        $this->assertSame('OpenCloseEventCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($openCloseEvent, $payload['openCloseEvent']);
    }

    public function testBuildStoredEvent(): void
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
        $this->assertSame('OpenCloseEventCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($openCloseEvent, $payload['openCloseEvent']);
    }
}
