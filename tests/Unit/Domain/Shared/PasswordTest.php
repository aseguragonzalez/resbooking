<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Password;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
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
        $expected = $this->faker->password(Password::MIN_LENGTH);

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

        $this->assertFalse(
            $password->equals(new Password($this->faker->password(Password::MIN_LENGTH)))
        );
    }

    public function testNewShouldCreateNewPassword(): void
    {
        $password = Password::new();

        $this->assertGreaterThanOrEqual(Password::MIN_LENGTH, strlen($password->getValue()));
    }
}
