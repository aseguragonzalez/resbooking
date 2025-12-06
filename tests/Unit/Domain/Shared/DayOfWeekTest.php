<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\DayOfWeek;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class DayOfWeekTest extends TestCase
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
        $id = $this->faker->numberBetween(1, 7);

        $dayOfWeek = DayOfWeek::getById($id);

        $this->assertSame($id, $dayOfWeek->value);
    }

    public function testGetByIdFailWhenIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getById(0);
    }

    public function testGetByName(): void
    {
        /** @var string $name */
        $name = $this->faker->randomElement([
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ]);

        $dayOfWeek = DayOfWeek::getByName($name);

        $this->assertSame($name, strtolower($dayOfWeek->name));
    }

    public function testGetByNameFailWhenIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getByName($this->faker->word());
    }

    public function testToString(): void
    {
        $dayOfWeek = DayOfWeek::Sunday;

        $this->assertSame('Sunday', $dayOfWeek->toString());
    }

    public function testAll(): void
    {
        $all = DayOfWeek::all();

        $this->assertCount(7, $all);
        $this->assertContains(DayOfWeek::Sunday, $all);
        $this->assertContains(DayOfWeek::Monday, $all);
        $this->assertContains(DayOfWeek::Tuesday, $all);
        $this->assertContains(DayOfWeek::Wednesday, $all);
        $this->assertContains(DayOfWeek::Thursday, $all);
        $this->assertContains(DayOfWeek::Friday, $all);
        $this->assertContains(DayOfWeek::Saturday, $all);
    }

    public function testGetByIdForAllCases(): void
    {
        $expectedCases = [
            1 => DayOfWeek::Sunday,
            2 => DayOfWeek::Monday,
            3 => DayOfWeek::Tuesday,
            4 => DayOfWeek::Wednesday,
            5 => DayOfWeek::Thursday,
            6 => DayOfWeek::Friday,
            7 => DayOfWeek::Saturday,
        ];

        foreach ($expectedCases as $id => $expectedCase) {
            $this->assertSame($expectedCase, DayOfWeek::getById($id));
        }
    }

    public function testGetByNameForAllCases(): void
    {
        $testCases = [
            'sunday' => DayOfWeek::Sunday,
            'monday' => DayOfWeek::Monday,
            'tuesday' => DayOfWeek::Tuesday,
            'wednesday' => DayOfWeek::Wednesday,
            'thursday' => DayOfWeek::Thursday,
            'friday' => DayOfWeek::Friday,
            'saturday' => DayOfWeek::Saturday,
            'Sunday' => DayOfWeek::Sunday,
            'MONDAY' => DayOfWeek::Monday,
            'TuEsDaY' => DayOfWeek::Tuesday,
        ];

        foreach ($testCases as $name => $expectedCase) {
            $this->assertSame($expectedCase, DayOfWeek::getByName($name));
        }
    }

    public function testGetByIdFailWhenIdIsTooLarge(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getById(8);
    }

    public function testGetByIdFailWhenIdIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getById(-1);
    }
}
