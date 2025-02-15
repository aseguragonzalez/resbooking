<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Phone;
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

    public function testPhoneShouldCreateInstance(): void
    {
        $expected = $this->faker->phoneNumber;

        $phone = new Phone($expected);

        $this->assertSame($expected, $phone->getValue());
    }

    public function testPhoneShouldBeCastedToString(): void
    {
        $expected = $this->faker->phoneNumber;

        $phone = new Phone($expected);

        $this->assertSame($expected, (string) $phone);
    }

    public function testPhoneShouldTrueWhenComparedWithSameValues(): void
    {
        $phone = new Phone($this->faker->phoneNumber);

        $this->assertTrue($phone->equals(new Phone($phone->getValue())));
    }

    public function testPhoneShouldFalseWhenComparedWithDifferentValues(): void
    {
        $phone = new Phone($this->faker->phoneNumber);

        $this->assertFalse($phone->equals(new Phone($this->faker->phoneNumber)));
    }
}
