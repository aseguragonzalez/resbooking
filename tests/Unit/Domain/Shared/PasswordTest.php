<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Password;

final class PasswordTest extends TestCase
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

    public function testPasswordShouldCreateInstance(): void
    {
        $expected = $this->faker->password(Password::MIN_LENGTH);

        $password = new Password($expected);

        $this->assertSame($expected, $password->getValue());
    }

    public function testPasswordShouldFailWhenValueInvalid(): void
    {
        $invalidPassword = substr($this->faker->password, 0, Password::MIN_LENGTH - 1);
        $this->expectException(\InvalidArgumentException::class);

        new Password(value: $invalidPassword);
    }

    public function testPasswordShouldBeCastedToString(): void
    {
        $expected = $this->faker->password;

        $password = new Password($expected);

        $this->assertSame($expected, (string) $password);
    }

    public function testPasswordShouldTrueWhenComparedWithSameValues(): void
    {
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $this->assertTrue($password->equals(new Password($password->getValue())));
    }

    public function testPasswordShouldFalseWhenComparedWithDifferentValues(): void
    {
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $this->assertFalse($password->equals(new Password($this->faker->password)));
    }
}
