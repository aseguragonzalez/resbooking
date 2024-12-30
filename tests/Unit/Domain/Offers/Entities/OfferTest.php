<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\Entities\Offer;
use App\Domain\Offers\Exceptions\InvalidDateRange;
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\Exceptions\ValueException;

final class OfferTest extends TestCase
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

    private function offer(
        array $openCloseEvents = [],
        array $turns = [],
        bool $isEnable = true
    ): Offer {
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        return new Offer(
            id: $this->faker->uuid,
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
            openCloseEvents: $openCloseEvents,
            turns: $turns,
            isEnable: $isEnable,
        );
    }

    public function testConstructorShouldCreateInstance(): void
    {
        $id = $this->faker->uuid;
        $description = $this->faker->text;
        $title = $this->faker->sentence;
        $termsAndConditions = $this->faker->text;
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        $offer = new Offer(
            id: $id,
            description: $description,
            title: $title,
            termsAndConditions: $termsAndConditions,
            startDate: $startDate,
            endDate: $endDate,
        );

        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals($id, $offer->getId());
        $this->assertEquals($description, $offer->getDescription());
        $this->assertEquals($title, $offer->getTitle());
        $this->assertEquals($termsAndConditions, $offer->getTermsAndConditions());
        $this->assertEquals($startDate, $offer->getStartDate());
        $this->assertEquals($endDate, $offer->getEndDate());
    }

    public function testConstructorShouldFailWhenTitleIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Offer(
            id: $this->faker->uuid,
            description: $this->faker->text,
            title: '',
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Offer(
            id: $this->faker->uuid,
            description: '',
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenTermsAndConditionsIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Offer(
            id: $this->faker->uuid,
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: '',
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenDateRangeIsInvalid(): void
    {
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->sub(new \DateInterval('P1D'));
        $this->expectException(InvalidDateRange::class);

        new Offer(
            id: $this->faker->uuid,
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
        );
    }

    public function testUpdateShouldFailWhenDescriptionIsInvalid(): void
    {
        $offer = $this->offer();

        $this->expectException(ValueException::class);

        $offer->update(
            description: '',
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: (new \DateTimeImmutable())->add(new \DateInterval('P10D')),
        );
    }

    public function testUpdateShouldFailWhenTitleIsInvalid(): void
    {
        $offer = $this->offer();

        $this->expectException(ValueException::class);

        $offer->update(
            description: $this->faker->text,
            title: '',
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: (new \DateTimeImmutable())->add(new \DateInterval('P10D')),
        );
    }

    public function testUpdateShouldFailWhenTermsAndConditionsIsInvalid(): void
    {
        $offer = $this->offer();

        $this->expectException(ValueException::class);

        $offer->update(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: '',
            startDate: new \DateTimeImmutable(),
            endDate: (new \DateTimeImmutable())->add(new \DateInterval('P10D')),
        );
    }

    public function testUpdateShouldFailWhenStartDateIsInvalid(): void
    {
        $offer = $this->offer();
        $endDate = $offer->getEndDate();
        $this->expectException(InvalidDateRange::class);

        $offer->update(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $endDate->add(new \DateInterval('P1D')),
            endDate: $endDate,
        );
    }

    public function testUpdateShouldFailWhenEndDateIsInvalid(): void
    {
        $offer = $this->offer();
        $startDate = $offer->getStartDate();
        $this->expectException(InvalidDateRange::class);

        $offer->update(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $startDate->sub(new \DateInterval('P1D')),
        );
    }

    public function testDisableShouldDisableOffer(): void
    {
        $offer = $this->offer();

        $offer->disable();

        $this->assertFalse($offer->isEnable());
    }

    public function testEnableShouldEnableOffer(): void
    {
        $offer = $this->offer(isEnable: false);

        $offer->enable();

        $this->assertTrue($offer->isEnable());
    }

    public function testAddTurnShouldAddTurnToOffer(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $offer->addTurn($turn);

        $this->assertContains($turn, $offer->getTurns());
    }

    public function testAddTurnShouldFailWhenTurnAlreadyExist(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
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
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $offer = $this->offer(turns: [$turn]);

        $offer->removeTurn($turn);

        $this->assertEmpty($offer->getTurns());
    }

    public function testRemoveTurnShouldFailWhenTurnDoesNotExist(): void
    {
        $offer = $this->offer();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
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
