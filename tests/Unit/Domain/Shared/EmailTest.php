<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

use App\Domain\Shared\Email;

final class EmailTest extends TestCase
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

	public function testEmailShouldCreateInstance(): void
	{
		$expected = $this->faker->email;

		$email = new Email($expected);

		$this->assertSame($expected, $email->value());
	}

	public function testEmailShouldFailWhenValueInvalid(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		new Email($this->faker->word);
	}

	public function testEmailShouldBeCastedToString(): void
	{
		$expected = $this->faker->email;

		$email = new Email($expected);

		$this->assertSame($expected, (string) $email);
	}

	public function testEmailShouldTrueWhenComparedWithSameValues(): void
	{
		$email = new Email($this->faker->email);

		$this->assertTrue($email->equals(new Email($email->value())));
	}

	public function testEmailShouldFalseWhenComparedWithDifferentValues(): void
	{
		$email = new Email($this->faker->email);

		$this->assertFalse($email->equals(new Email($this->faker->email)));
	}
}
