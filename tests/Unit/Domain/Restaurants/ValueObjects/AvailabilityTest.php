<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\ValueObjects;

use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\TestCase;

final class AvailabilityTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $capacity = new Capacity(100);
        $dayOfWeek = DayOfWeek::Monday;
        $timeSlot = TimeSlot::H1200;

        $availability = new Availability(
            capacity: $capacity,
            dayOfWeek: $dayOfWeek,
            timeSlot: $timeSlot
        );

        $this->assertInstanceOf(Availability::class, $availability);
        $this->assertSame($capacity, $availability->capacity);
        $this->assertSame($dayOfWeek, $availability->dayOfWeek);
        $this->assertSame($timeSlot, $availability->timeSlot);
    }

    public function testEqualsReturnsTrueWhenSameDayAndTimeSlot(): void
    {
        $availability1 = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $availability2 = new Availability(
            capacity: new Capacity(200),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $this->assertTrue($availability1->equals($availability2));
    }

    public function testEqualsReturnsFalseWhenDifferentDay(): void
    {
        $availability1 = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $availability2 = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Tuesday,
            timeSlot: TimeSlot::H1200
        );

        $this->assertFalse($availability1->equals($availability2));
    }

    public function testEqualsReturnsFalseWhenDifferentTimeSlot(): void
    {
        $availability1 = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $availability2 = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1230
        );

        $this->assertFalse($availability1->equals($availability2));
    }
}
