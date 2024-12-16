<?php

declare(strict_types=1);

use App\Domain\Shared\DayOfWeek;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class DayOfWeekTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();

        DayOfWeek::initialize();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testShouldRetrieveDayOfWeekById(): void
    {
        $id = $this->faker->numberBetween(1, 7);

        $dayOfWeek = DayOfWeek::byId($id);

        $this->assertSame($id, $dayOfWeek->id);
    }

    public function testShouldRaiseExceptionWhenRetrieveDayOfWeekByIdWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DayOfWeek::byId(0);
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

        $dayOfWeek = DayOfWeek::byName($name);

        $this->assertSame($name, $dayOfWeek->name);
    }
}
