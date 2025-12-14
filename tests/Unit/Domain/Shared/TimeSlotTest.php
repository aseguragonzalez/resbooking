<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\TimeSlot;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TimeSlotTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testGetById(): void
    {
        $id = $this->faker->numberBetween(1, 24);

        $timeSlot = TimeSlot::getById($id);

        $this->assertSame($id, $timeSlot->value);
    }

    public function testGetByIdFailWhenIdIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TimeSlot::getById(0);
    }

    public function testGetByStartTime(): void
    {
        /** @var string $startTime */
        $startTime = $this->faker->randomElement([
            '12:00:00',
            '12:30:00',
            '13:00:00',
            '13:30:00',
            '14:00:00',
            '14:30:00',
            '15:00:00',
            '15:30:00',
            '16:00:00',
            '16:30:00',
            '17:00:00',
            '17:30:00',
            '18:00:00',
            '18:30:00',
            '19:00:00',
            '19:30:00',
            '20:00:00',
            '20:30:00',
            '21:00:00',
            '21:30:00',
            '22:00:00',
            '22:30:00',
            '23:00:00',
            '23:30:00',
        ]);

        $timeSlot = TimeSlot::getByStartTime($startTime);

        $this->assertSame($startTime, $timeSlot->toString());
    }

    public function testGetByStartTimeFailWhenValueIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TimeSlot::getByStartTime($this->faker->lexify('?????'));
    }

    /**
     * @return array<array{TimeSlot, string}>
     */
    public static function timeSlotToStringProvider(): array
    {
        return [
            [TimeSlot::H1200, '12:00:00'],
            [TimeSlot::H1230, '12:30:00'],
            [TimeSlot::H1300, '13:00:00'],
            [TimeSlot::H1330, '13:30:00'],
            [TimeSlot::H1400, '14:00:00'],
            [TimeSlot::H1430, '14:30:00'],
            [TimeSlot::H1500, '15:00:00'],
            [TimeSlot::H1530, '15:30:00'],
            [TimeSlot::H1600, '16:00:00'],
            [TimeSlot::H1630, '16:30:00'],
            [TimeSlot::H1700, '17:00:00'],
            [TimeSlot::H1730, '17:30:00'],
            [TimeSlot::H1800, '18:00:00'],
            [TimeSlot::H1830, '18:30:00'],
            [TimeSlot::H1900, '19:00:00'],
            [TimeSlot::H1930, '19:30:00'],
            [TimeSlot::H2000, '20:00:00'],
            [TimeSlot::H2030, '20:30:00'],
            [TimeSlot::H2100, '21:00:00'],
            [TimeSlot::H2130, '21:30:00'],
            [TimeSlot::H2200, '22:00:00'],
            [TimeSlot::H2230, '22:30:00'],
            [TimeSlot::H2300, '23:00:00'],
            [TimeSlot::H2330, '23:30:00'],
        ];
    }

    #[DataProvider('timeSlotToStringProvider')]
    public function testToString(TimeSlot $timeSlot, string $expectedTime): void
    {
        $this->assertSame($expectedTime, $timeSlot->toString());
    }

    public function testAll(): void
    {
        $all = TimeSlot::all();

        $this->assertCount(24, $all);
        $this->assertContains(TimeSlot::H1200, $all);
        $this->assertContains(TimeSlot::H1230, $all);
        $this->assertContains(TimeSlot::H1300, $all);
        $this->assertContains(TimeSlot::H1330, $all);
        $this->assertContains(TimeSlot::H1400, $all);
        $this->assertContains(TimeSlot::H1430, $all);
        $this->assertContains(TimeSlot::H1500, $all);
        $this->assertContains(TimeSlot::H1530, $all);
        $this->assertContains(TimeSlot::H1600, $all);
        $this->assertContains(TimeSlot::H1630, $all);
        $this->assertContains(TimeSlot::H1700, $all);
        $this->assertContains(TimeSlot::H1730, $all);
        $this->assertContains(TimeSlot::H1800, $all);
        $this->assertContains(TimeSlot::H1830, $all);
        $this->assertContains(TimeSlot::H1900, $all);
        $this->assertContains(TimeSlot::H1930, $all);
        $this->assertContains(TimeSlot::H2000, $all);
        $this->assertContains(TimeSlot::H2030, $all);
        $this->assertContains(TimeSlot::H2100, $all);
        $this->assertContains(TimeSlot::H2130, $all);
        $this->assertContains(TimeSlot::H2200, $all);
        $this->assertContains(TimeSlot::H2230, $all);
        $this->assertContains(TimeSlot::H2300, $all);
        $this->assertContains(TimeSlot::H2330, $all);
    }

    /**
     * @return array<array{int, TimeSlot}>
     */
    public static function timeSlotGetByIdProvider(): array
    {
        return [
            [1, TimeSlot::H1200],
            [2, TimeSlot::H1230],
            [3, TimeSlot::H1300],
            [4, TimeSlot::H1330],
            [5, TimeSlot::H1400],
            [6, TimeSlot::H1430],
            [7, TimeSlot::H1500],
            [8, TimeSlot::H1530],
            [9, TimeSlot::H1600],
            [10, TimeSlot::H1630],
            [11, TimeSlot::H1700],
            [12, TimeSlot::H1730],
            [13, TimeSlot::H1800],
            [14, TimeSlot::H1830],
            [15, TimeSlot::H1900],
            [16, TimeSlot::H1930],
            [17, TimeSlot::H2000],
            [18, TimeSlot::H2030],
            [19, TimeSlot::H2100],
            [20, TimeSlot::H2130],
            [21, TimeSlot::H2200],
            [22, TimeSlot::H2230],
            [23, TimeSlot::H2300],
            [24, TimeSlot::H2330],
        ];
    }

    #[DataProvider('timeSlotGetByIdProvider')]
    public function testGetByIdForAllCases(int $id, TimeSlot $expectedCase): void
    {
        $this->assertSame($expectedCase, TimeSlot::getById($id));
    }

    /**
     * @return array<array{string, TimeSlot}>
     */
    public static function timeSlotGetByStartTimeProvider(): array
    {
        return [
            ['12:00:00', TimeSlot::H1200],
            ['12:30:00', TimeSlot::H1230],
            ['13:00:00', TimeSlot::H1300],
            ['13:30:00', TimeSlot::H1330],
            ['14:00:00', TimeSlot::H1400],
            ['14:30:00', TimeSlot::H1430],
            ['15:00:00', TimeSlot::H1500],
            ['15:30:00', TimeSlot::H1530],
            ['16:00:00', TimeSlot::H1600],
            ['16:30:00', TimeSlot::H1630],
            ['17:00:00', TimeSlot::H1700],
            ['17:30:00', TimeSlot::H1730],
            ['18:00:00', TimeSlot::H1800],
            ['18:30:00', TimeSlot::H1830],
            ['19:00:00', TimeSlot::H1900],
            ['19:30:00', TimeSlot::H1930],
            ['20:00:00', TimeSlot::H2000],
            ['20:30:00', TimeSlot::H2030],
            ['21:00:00', TimeSlot::H2100],
            ['21:30:00', TimeSlot::H2130],
            ['22:00:00', TimeSlot::H2200],
            ['22:30:00', TimeSlot::H2230],
            ['23:00:00', TimeSlot::H2300],
            ['23:30:00', TimeSlot::H2330],
        ];
    }

    #[DataProvider('timeSlotGetByStartTimeProvider')]
    public function testGetByStartTimeForAllCases(string $startTime, TimeSlot $expectedCase): void
    {
        $this->assertSame($expectedCase, TimeSlot::getByStartTime($startTime));
    }

    /**
     * @return array<array{string, TimeSlot}>
     */
    public static function timeSlotGetByStartTimeDifferentFormatsProvider(): array
    {
        return [
            ['12:00', TimeSlot::H1200],
            ['12:00:00', TimeSlot::H1200],
            ['12:30', TimeSlot::H1230],
            ['12:30:00', TimeSlot::H1230],
            ['23:00', TimeSlot::H2300],
            ['23:00:00', TimeSlot::H2300],
            ['23:30', TimeSlot::H2330],
            ['23:30:00', TimeSlot::H2330],
        ];
    }

    #[DataProvider('timeSlotGetByStartTimeDifferentFormatsProvider')]
    public function testGetByStartTimeWithDifferentFormats(string $startTime, TimeSlot $expectedCase): void
    {
        $this->assertSame($expectedCase, TimeSlot::getByStartTime($startTime));
    }

    public function testGetByIdFailWhenIdIsTooLarge(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TimeSlot::getById(25);
    }

    public function testGetByIdFailWhenIdIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TimeSlot::getById(-1);
    }

    /**
     * @return array<array{string}>
     */
    public static function invalidTimeFormatProvider(): array
    {
        return [
            ['11:00:00'],
            ['24:00:00'],
            ['25:00:00'],
            ['12:60:00'],
            ['invalid'],
            ['12'],
            ['12:'],
        ];
    }

    #[DataProvider('invalidTimeFormatProvider')]
    public function testGetByStartTimeFailWithInvalidFormat(string $invalidFormat): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid time slot start time');

        TimeSlot::getByStartTime($invalidFormat);
    }
}
