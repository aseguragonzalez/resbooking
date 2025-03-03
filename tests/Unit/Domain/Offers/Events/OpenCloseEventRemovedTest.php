<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use App\Domain\Offers\Events\OpenCloseEventRemoved;
use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OpenCloseEventRemovedTest extends TestCase
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
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $event = OpenCloseEventRemoved::new(
            offerId: $offerId,
            openCloseEvent: $openCloseEvent
        );

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OpenCloseEventRemoved', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($offerId, $payload['offerId']);
        $this->assertSame($openCloseEvent, $payload['openCloseEvent']);
    }

    public function testBuildStoredEvent(): void
    {
        $offerId = $this->faker->uuid;
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $event = OpenCloseEventRemoved::build(
            offerId: $offerId,
            openCloseEvent: $openCloseEvent,
            id: $this->faker->uuid
        );

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OpenCloseEventRemoved', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($offerId, $payload['offerId']);
        $this->assertSame($openCloseEvent, $payload['openCloseEvent']);
    }
}
