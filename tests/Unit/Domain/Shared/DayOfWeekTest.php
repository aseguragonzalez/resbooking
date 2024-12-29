<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\DayOfWeek;

final class DayOfWeekTest extends TestCase
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

    public function testShouldRetrieveDayOfWeekById(): void
    {
        $id = $this->faker->numberBetween(1, 7);

        $dayOfWeek = DayOfWeek::getById($id);

        $this->assertSame($id, $dayOfWeek->value);
    }

    public function testShouldRaiseExceptionWhenRetrieveDayOfWeekByIdWithInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getById(0);
    }

    public function testShouldRetrieveDayOfWeekByName(): void
    {
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

    public function testShouldRaiseExceptionWhenRetrieveDayOfWeekByNameWithInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DayOfWeek::getByName($this->faker->word());
    }
}
