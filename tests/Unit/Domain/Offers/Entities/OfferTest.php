<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Entities;

use App\Domain\Offers\Entities\Offer;
use App\Domain\Offers\Events\{
    OfferCreated,
    OfferUpdated,
    OfferDisabled,
    OfferEnabled,
    OpenCloseEventCreated,
    OpenCloseEventRemoved,
    TurnAssigned,
    TurnUnassigned
};
use App\Domain\Offers\Exceptions\{
    OfferAlreadyDisabled,
    OfferAlreadyEnabled,
};
use App\Domain\Offers\ValueObjects\{Project, Settings};
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
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
        return Offer::stored(
            id: $this->faker->uuid,
            available: $available,
            openCloseEvents: $openCloseEvents,
            project: $this->project,
            settings: $this->settings,
            turns: $turns,
        );
    }

    public function testNewShouldCreateInstance(): void
    {
        $offer = Offer::new(project: $this->project, settings: $this->settings);

        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertNotEmpty($offer->getId());
        $this->assertEquals($this->project, $offer->project);
        $this->assertEquals($this->settings, $offer->getSettings());
        $this->assertEmpty($offer->getOpenCloseEvents());
        $this->assertEmpty($offer->getTurns());
        $this->assertTrue($offer->isAvailable());

        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferCreated::class, $event);
        $payload = $event->getPayload();
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($offer, $payload["offer"]);
    }

    public function testSetSettingsShouldUpdateOfferSettings(): void
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

        $this->assertEquals($newSetting, $offer->getSettings());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferUpdated::class, $event);
        $payload = $event->getPayload();
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($offer, $payload["offer"]);
    }

    public function testDisableShouldSetUnavailableIt(): void
    {
        $offer = $this->offer();

        $offer->disable();

        $this->assertFalse($offer->isAvailable());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferDisabled::class, $event);
        $payload = $event->getPayload();
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($offer, $payload["offer"]);
    }

    public function testDisableShouldFailWhenOfferIsAlreadyDisabled(): void
    {
        $offer = $this->offer(available: false);
        $this->expectException(OfferAlreadyDisabled::class);

        $offer->disable();
    }

    public function testEnableShouldSetAvailableIt(): void
    {
        $offer = $this->offer(available: false);

        $offer->enable();

        $this->assertTrue($offer->isAvailable());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(OfferEnabled::class, $event);
        $payload = $event->getPayload();
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($offer, $payload["offer"]);
    }

    public function testEnabledShouldFailWhenOfferIsAlreadyEnabled(): void
    {
        $offer = $this->offer();
        $this->expectException(OfferAlreadyEnabled::class);

        $offer->enable();
    }

    public function testAddTurnShouldAddTurnToOffer(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $offer->addTurn($turn);

        $this->assertContains($turn, $offer->getTurns());
        $events = $offer->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(TurnAssigned::class, $event);
        $payload = $event->getPayload();
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($turn, $payload["turn"]);
    }

    public function testAddTurnShouldFailWhenTurnAlreadyExist(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $offer = $this->offer(turns: [$turn]);
        $this->expectException(TurnAlreadyExist::class);

        $offer->addTurn($turn);
    }

    public function testRemoveTurnShouldRemoveTurnFromOffer(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
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
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($turn, $payload["turn"]);
    }

    public function testRemoveTurnShouldFailWhenTurnDoesNotExist(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $this->expectException(TurnDoesNotExist::class);

        $offer->removeTurn($turn);
    }

    public function testAddOpenCloseEventShouldAddOpenCloseEventToOffer(): void
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
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($openCloseEvent, $payload["openCloseEvent"]);
    }

    public function testAddOpenCloseEventShouldFailWhenOpenCloseEventAlreadyExist(): void
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

    public function testAddOpenCloseEventShouldFailWhenDateIsOutOfRange(): void
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

    public function testRemoveOpenCloseEventShouldRemoveOpenCloseEventFromOffer(): void
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
        $this->assertEquals($offer->getId(), $payload["offerId"]);
        $this->assertEquals($openCloseEvent, $payload["openCloseEvent"]);
    }

    public function testRemoveOpenCloseEventShouldFailWhenOpenCloseEventDoesNotExist(): void
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
