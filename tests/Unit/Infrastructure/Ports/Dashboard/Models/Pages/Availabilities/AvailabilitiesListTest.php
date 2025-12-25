<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Availabilities\Pages;

use PHPUnit\Framework\TestCase;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Pages\AvailabilitiesList;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Availability as AvailabilityModel;

final class AvailabilitiesListTest extends TestCase
{
    public function testCreateWithEmptyArray(): void
    {
        $list = AvailabilitiesList::create([]);

        $this->assertSame('{{availabilities.title}}', $list->pageTitle);
        $this->assertEmpty($list->availabilities);
    }

    public function testCreateWithSingleAvailability(): void
    {
        $domainAvailability = new Availability(
            capacity: new Capacity(10),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $list = AvailabilitiesList::create([$domainAvailability]);

        $this->assertSame('{{availabilities.title}}', $list->pageTitle);
        $this->assertCount(1, $list->availabilities);
        $this->assertInstanceOf(AvailabilityModel::class, $list->availabilities[0]);
        $this->assertSame('12:00', $list->availabilities[0]->time);
        $this->assertSame(DayOfWeek::Monday->value, $list->availabilities[0]->dayOfWeekId);
        $this->assertSame(TimeSlot::H1200->value, $list->availabilities[0]->timeSlotId);
        $this->assertSame(10, $list->availabilities[0]->capacity);
        $this->assertSame('1_2', $list->availabilities[0]->id);
    }

    public function testCreateSortsByTimeSlotIdThenDayOfWeekId(): void
    {
        $availability1 = new Availability(
            capacity: new Capacity(10),
            dayOfWeek: DayOfWeek::Wednesday,
            timeSlot: TimeSlot::H1300
        );
        $availability2 = new Availability(
            capacity: new Capacity(15),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1300
        );
        $availability3 = new Availability(
            capacity: new Capacity(20),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $list = AvailabilitiesList::create([$availability1, $availability2, $availability3]);

        $this->assertCount(3, $list->availabilities);
        $this->assertSame(TimeSlot::H1200->value, $list->availabilities[0]->timeSlotId);
        $this->assertSame(DayOfWeek::Monday->value, $list->availabilities[0]->dayOfWeekId);
        $this->assertSame(TimeSlot::H1300->value, $list->availabilities[1]->timeSlotId);
        $this->assertSame(DayOfWeek::Monday->value, $list->availabilities[1]->dayOfWeekId);
        $this->assertSame(TimeSlot::H1300->value, $list->availabilities[2]->timeSlotId);
        $this->assertSame(DayOfWeek::Wednesday->value, $list->availabilities[2]->dayOfWeekId);
    }

    public function testCreateExtractsTimeFromTimeSlot(): void
    {
        $availability = new Availability(
            capacity: new Capacity(5),
            dayOfWeek: DayOfWeek::Friday,
            timeSlot: TimeSlot::H1830
        );

        $list = AvailabilitiesList::create([$availability]);

        $this->assertSame('18:30', $list->availabilities[0]->time);
    }

    public function testCreateSetsCorrectIdFormat(): void
    {
        $availability = new Availability(
            capacity: new Capacity(25),
            dayOfWeek: DayOfWeek::Saturday,
            timeSlot: TimeSlot::H2000
        );

        $list = AvailabilitiesList::create([$availability]);

        $expectedId = TimeSlot::H2000->value . '_' . DayOfWeek::Saturday->value;
        $this->assertSame($expectedId, $list->availabilities[0]->id);
    }
}
