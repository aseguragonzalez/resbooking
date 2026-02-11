<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyValueObject;

final class ValueObjectTest extends TestCase
{
    public function testEqualsReturnsTrueForSameValue(): void
    {
        $vo1 = new DummyValueObject('value-a');
        $vo2 = new DummyValueObject('value-a');

        $this->assertTrue($vo1->equals($vo2));
    }

    public function testEqualsReturnsFalseForDifferentValue(): void
    {
        $vo1 = new DummyValueObject('value-a');
        $vo2 = new DummyValueObject('value-b');

        $this->assertFalse($vo1->equals($vo2));
    }

    public function testEqualsReturnsFalseWhenNotInstanceOfSameClass(): void
    {
        $vo = new DummyValueObject('value');
        $other = new \Domain\Restaurants\ValueObjects\Availability(
            capacity: new \Domain\Shared\Capacity(10),
            dayOfWeek: \Domain\Shared\DayOfWeek::Monday,
            timeSlot: \Domain\Shared\TimeSlot::H1200
        );

        $this->assertFalse($vo->equals($other));
    }
}
