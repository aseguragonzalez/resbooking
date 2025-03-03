<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\DayOfWeek;
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
}
