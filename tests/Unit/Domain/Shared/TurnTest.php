<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\Turn;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TurnTest extends TestCase
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

        $turn = Turn::getById($id);

        $this->assertSame($id, $turn->value);
    }

    public function testGetByIdFailWhenIdIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getById(0);
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

        $turn = Turn::getByStartTime($startTime);

        $this->assertSame($startTime, $turn->toString());
    }

    public function testGetByStartTimeFailWhenValueIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getByStartTime($this->faker->lexify('?????'));
    }

    /**
     * @return array<array{Turn, string}>
     */
    public static function turnToStringProvider(): array
    {
        return [
            [Turn::H1200, '12:00:00'],
            [Turn::H1230, '12:30:00'],
            [Turn::H1300, '13:00:00'],
            [Turn::H1330, '13:30:00'],
            [Turn::H1400, '14:00:00'],
            [Turn::H1430, '14:30:00'],
            [Turn::H1500, '15:00:00'],
            [Turn::H1530, '15:30:00'],
            [Turn::H1600, '16:00:00'],
            [Turn::H1630, '16:30:00'],
            [Turn::H1700, '17:00:00'],
            [Turn::H1730, '17:30:00'],
            [Turn::H1800, '18:00:00'],
            [Turn::H1830, '18:30:00'],
            [Turn::H1900, '19:00:00'],
            [Turn::H1930, '19:30:00'],
            [Turn::H2000, '20:00:00'],
            [Turn::H2030, '20:30:00'],
            [Turn::H2100, '21:00:00'],
            [Turn::H2130, '21:30:00'],
            [Turn::H2200, '22:00:00'],
            [Turn::H2230, '22:30:00'],
            [Turn::H2300, '23:00:00'],
            [Turn::H2330, '23:30:00'],
        ];
    }

    #[DataProvider('turnToStringProvider')]
    public function testToString(Turn $turn, string $expectedTime): void
    {
        $this->assertSame($expectedTime, $turn->toString());
    }

    public function testAll(): void
    {
        $all = Turn::all();

        $this->assertCount(24, $all);
        $this->assertContains(Turn::H1200, $all);
        $this->assertContains(Turn::H1230, $all);
        $this->assertContains(Turn::H1300, $all);
        $this->assertContains(Turn::H1330, $all);
        $this->assertContains(Turn::H1400, $all);
        $this->assertContains(Turn::H1430, $all);
        $this->assertContains(Turn::H1500, $all);
        $this->assertContains(Turn::H1530, $all);
        $this->assertContains(Turn::H1600, $all);
        $this->assertContains(Turn::H1630, $all);
        $this->assertContains(Turn::H1700, $all);
        $this->assertContains(Turn::H1730, $all);
        $this->assertContains(Turn::H1800, $all);
        $this->assertContains(Turn::H1830, $all);
        $this->assertContains(Turn::H1900, $all);
        $this->assertContains(Turn::H1930, $all);
        $this->assertContains(Turn::H2000, $all);
        $this->assertContains(Turn::H2030, $all);
        $this->assertContains(Turn::H2100, $all);
        $this->assertContains(Turn::H2130, $all);
        $this->assertContains(Turn::H2200, $all);
        $this->assertContains(Turn::H2230, $all);
        $this->assertContains(Turn::H2300, $all);
        $this->assertContains(Turn::H2330, $all);
    }

    /**
     * @return array<array{int, Turn}>
     */
    public static function turnGetByIdProvider(): array
    {
        return [
            [1, Turn::H1200],
            [2, Turn::H1230],
            [3, Turn::H1300],
            [4, Turn::H1330],
            [5, Turn::H1400],
            [6, Turn::H1430],
            [7, Turn::H1500],
            [8, Turn::H1530],
            [9, Turn::H1600],
            [10, Turn::H1630],
            [11, Turn::H1700],
            [12, Turn::H1730],
            [13, Turn::H1800],
            [14, Turn::H1830],
            [15, Turn::H1900],
            [16, Turn::H1930],
            [17, Turn::H2000],
            [18, Turn::H2030],
            [19, Turn::H2100],
            [20, Turn::H2130],
            [21, Turn::H2200],
            [22, Turn::H2230],
            [23, Turn::H2300],
            [24, Turn::H2330],
        ];
    }

    #[DataProvider('turnGetByIdProvider')]
    public function testGetByIdForAllCases(int $id, Turn $expectedCase): void
    {
        $this->assertSame($expectedCase, Turn::getById($id));
    }

    /**
     * @return array<array{string, Turn}>
     */
    public static function turnGetByStartTimeProvider(): array
    {
        return [
            ['12:00:00', Turn::H1200],
            ['12:30:00', Turn::H1230],
            ['13:00:00', Turn::H1300],
            ['13:30:00', Turn::H1330],
            ['14:00:00', Turn::H1400],
            ['14:30:00', Turn::H1430],
            ['15:00:00', Turn::H1500],
            ['15:30:00', Turn::H1530],
            ['16:00:00', Turn::H1600],
            ['16:30:00', Turn::H1630],
            ['17:00:00', Turn::H1700],
            ['17:30:00', Turn::H1730],
            ['18:00:00', Turn::H1800],
            ['18:30:00', Turn::H1830],
            ['19:00:00', Turn::H1900],
            ['19:30:00', Turn::H1930],
            ['20:00:00', Turn::H2000],
            ['20:30:00', Turn::H2030],
            ['21:00:00', Turn::H2100],
            ['21:30:00', Turn::H2130],
            ['22:00:00', Turn::H2200],
            ['22:30:00', Turn::H2230],
            ['23:00:00', Turn::H2300],
            ['23:30:00', Turn::H2330],
        ];
    }

    #[DataProvider('turnGetByStartTimeProvider')]
    public function testGetByStartTimeForAllCases(string $startTime, Turn $expectedCase): void
    {
        $this->assertSame($expectedCase, Turn::getByStartTime($startTime));
    }

    /**
     * @return array<array{string, Turn}>
     */
    public static function turnGetByStartTimeDifferentFormatsProvider(): array
    {
        return [
            ['12:00', Turn::H1200],
            ['12:00:00', Turn::H1200],
            ['12:30', Turn::H1230],
            ['12:30:00', Turn::H1230],
            ['23:00', Turn::H2300],
            ['23:00:00', Turn::H2300],
            ['23:30', Turn::H2330],
            ['23:30:00', Turn::H2330],
        ];
    }

    #[DataProvider('turnGetByStartTimeDifferentFormatsProvider')]
    public function testGetByStartTimeWithDifferentFormats(string $startTime, Turn $expectedCase): void
    {
        $this->assertSame($expectedCase, Turn::getByStartTime($startTime));
    }

    public function testGetByIdFailWhenIdIsTooLarge(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getById(25);
    }

    public function testGetByIdFailWhenIdIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getById(-1);
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
        $this->expectExceptionMessage('Invalid turn start time');

        Turn::getByStartTime($invalidFormat);
    }
}
