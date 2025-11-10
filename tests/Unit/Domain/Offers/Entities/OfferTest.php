<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Entities;

use Domain\Offers\Entities\Offer;
use Domain\Offers\Events\{
    OfferCreated,
    OfferUpdated,
    OfferDisabled,
    OfferEnabled,
    OpenCloseEventCreated,
    OpenCloseEventRemoved,
    TurnAssigned,
    TurnUnassigned
};
use Domain\Offers\Exceptions\{
    OfferAlreadyDisabled,
    OfferAlreadyEnabled,
};
use Domain\Offers\ValueObjects\{Project, Settings};
use Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use Domain\Shared\{Capacity, DayOfWeek, Turn};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OfferTest extends TestCase
{
    private Faker $faker;
    private Project $project;
    private Settings $settings;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->project = new Project($this->faker->uuid);
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        $this->settings = new Settings(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
        );
    }

    protected function tearDown(): void
    {
    }

    /**
     * @param array<OpenCloseEvent> $openCloseEvents
     * @param array<TurnAvailability> $turns
     */
    private function offer(array $openCloseEvents = [], array $turns = [], bool $available = true): Offer
    {
        return Offer::build(
            id: $this->faker->uuid,
            available: $available,
            openCloseEvents: $openCloseEvents,
            project: $this->project,
            settings: $this->settings,
            turns: $turns,
        );
    }

    public function testCreateInstance(): void
    {
        $offer = Offer::new(project: $this->project, settings: $this->settings);

        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertNotEmpty($offer->getId());
        $this->assertSame($this->project, $offer->project);
        $this->assertSame($this->settings, $offer->getSettings());
        $this->assertEmpty($offer->getOpenCloseEvents());
        $this->assertEmpty($offer->getTurns());
        $this->assertTrue($offer->isAvailable());

        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferCreated::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($offer, $payload["offer"]);
    }

    public function testUpdateOfferSettings(): void
    {
        $offer = $this->offer();
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        $newSetting = new Settings(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
        );

        $offer->setSettings($newSetting);

        $this->assertSame($newSetting, $offer->getSettings());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferUpdated::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($offer, $payload["offer"]);
    }

    public function testSetAsDisabled(): void
    {
        $offer = $this->offer();

        $offer->disable();

        $this->assertFalse($offer->isAvailable());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferDisabled::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($offer, $payload["offer"]);
    }

    public function testSetAsDisabledFailsWhenOfferIsAlreadyDisabled(): void
    {
        $offer = $this->offer(available: false);
        $this->expectException(OfferAlreadyDisabled::class);

        $offer->disable();
    }

    public function testSetAsEnabled(): void
    {
        $offer = $this->offer(available: false);

        $offer->enable();

        $this->assertTrue($offer->isAvailable());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferEnabled::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($offer, $payload["offer"]);
    }

    public function testSetAsEnabledFailsWhenOfferIsAlreadyEnabled(): void
    {
        $offer = $this->offer();
        $this->expectException(OfferAlreadyEnabled::class);

        $offer->enable();
    }

    public function testAddTurnToOffer(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $offer->addTurn($turn);

        $this->assertContains($turn, $offer->getTurns());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(TurnAssigned::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($turn, $payload["turn"]);
    }

    public function testAddTurnFailWhenTurnAlreadyExist(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $offer = $this->offer(turns: [$turn]);
        $this->expectException(TurnAlreadyExist::class);

        $offer->addTurn($turn);
    }

    public function testRemoveTurnFromOffer(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $offer = $this->offer(turns: [$turn]);

        $offer->removeTurn($turn);

        $this->assertEmpty($offer->getTurns());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(TurnUnassigned::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($turn, $payload["turn"]);
    }

    public function testRemoveTurnFailWhenTurnDoesNotExist(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $this->expectException(TurnDoesNotExist::class);

        $offer->removeTurn($turn);
    }

    public function testAddOpenCloseEventToOffer(): void
    {
        $offer = $this->offer();
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $offer->addOpenCloseEvent($openCloseEvent);

        $this->assertContains($openCloseEvent, $offer->getOpenCloseEvents());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OpenCloseEventCreated::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($openCloseEvent, $payload["openCloseEvent"]);
    }

    public function testAddOpenCloseEventFailWhenOpenCloseEventAlreadyExist(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $offer = $this->offer(openCloseEvents: [$openCloseEvent]);
        $this->expectException(OpenCloseEventAlreadyExist::class);

        $offer->addOpenCloseEvent($openCloseEvent);
    }

    public function testAddOpenCloseEventFailWhenDateIsOutOfRange(): void
    {
        $offer = $this->offer();
        $date = new \DateTimeImmutable();
        $this->expectException(OpenCloseEventOutOfRange::class);

        $offer->addOpenCloseEvent(new OpenCloseEvent(
            date: $date->sub(new \DateInterval('P1D')),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        ));
    }

    public function testRemoveOpenCloseEventFromOffer(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $offer = $this->offer(openCloseEvents: [$openCloseEvent]);

        $offer->removeOpenCloseEvent($openCloseEvent);

        $this->assertEmpty($offer->getOpenCloseEvents());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OpenCloseEventRemoved::class, $event);
        $payload = $event->getPayload();
        $this->assertSame($offer->getId(), $payload["offerId"]);
        $this->assertSame($openCloseEvent, $payload["openCloseEvent"]);
    }

    public function testRemoveOpenCloseEventFailWhenOpenCloseEventDoesNotExist(): void
    {
        $offer = $this->offer();
        $this->expectException(OpenCloseEventDoesNotExist::class);

        $offer->removeOpenCloseEvent(new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        ));
    }
}
