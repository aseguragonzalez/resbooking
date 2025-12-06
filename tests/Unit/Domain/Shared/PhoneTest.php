<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\Phone;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
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
        $expected = $this->faker->phoneNumber;

        $phone = new Phone($expected);

        $this->assertSame($expected, $phone->value);
    }

    public function testCastingToString(): void
    {
        $expected = $this->faker->phoneNumber;

        $phone = new Phone($expected);

        $this->assertSame($expected, (string) $phone);
    }

    public function testEquals(): void
    {
        $phone = new Phone($this->faker->phoneNumber);

        $this->assertTrue($phone->equals(new Phone($phone->value)));
    }

    public function testEqualsIsFalseWhenComparedWithDifferentValues(): void
    {
        $phone = new Phone($this->faker->phoneNumber);

        $this->assertFalse($phone->equals(new Phone($this->faker->phoneNumber)));
    }
}
