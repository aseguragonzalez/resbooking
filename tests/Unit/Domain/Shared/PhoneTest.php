<?php

declare(strict_types=1);

use App\Domain\Shared\Phone;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
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

	public function testPhoneShouldCreateInstance(): void
	{
		$expected = $this->faker->phoneNumber;

		$phone = new Phone($expected);

		$this->assertSame($expected, $phone->value());
	}

	public function testPhoneShouldFailWhenValueInvalid(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Phone($this->faker->word);
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

		$this->assertTrue($phone->equals(new Phone($phone->value())));
	}

	public function testPhoneShouldFalseWhenComparedWithDifferentValues(): void
	{
		$phone = new Phone($this->faker->phoneNumber);

		$this->assertFalse($phone->equals(new Phone($this->faker->phoneNumber)));
	}
}
