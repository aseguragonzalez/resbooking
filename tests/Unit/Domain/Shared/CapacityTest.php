<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class CapacityTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateInstance(): void
    {
        $value = $this->faker->numberBetween(0, 100);

        $capacity = new Capacity($value);

        $this->assertSame($value, $capacity->value);
    }

    public function testCreateInstanceFailWhenValueIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Capacity($this->faker->numberBetween(-100, -1));
    }

    public function testCreateInstanceWithZero(): void
    {
        $capacity = new Capacity(0);

        $this->assertSame(0, $capacity->value);
    }

    public function testToString(): void
    {
        $value = $this->faker->numberBetween(0, 100);
        $capacity = new Capacity($value);

        $this->assertSame((string) $value, (string) $capacity);
    }

    public function testEquals(): void
    {
        $value = $this->faker->numberBetween(0, 100);
        $capacity = new Capacity($value);

        $this->assertTrue($capacity->equals(new Capacity($value)));
    }

    public function testEqualsIsFalseWhenComparedWithDifferentValues(): void
    {
        $value1 = $this->faker->numberBetween(1, 50);
        $value2 = $this->faker->numberBetween(51, 100);
        $capacity = new Capacity($value1);

        $this->assertFalse($capacity->equals(new Capacity($value2)));
    }
}
